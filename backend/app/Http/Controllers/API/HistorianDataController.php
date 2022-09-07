<?php
/**
* historian数据获取控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SIS\HistorianTag;
use HistorianService;
use Illuminate\Http\Request;
use UtilService;
use Log;

class HistorianDataController extends Controller
{

    private function getTagList($tagIds)
    {
        $tagsIdList = explode(',', $tagIds);
        $tags = HistorianTag::whereIn('id', $tagsIdList)->get();
        $tagsNameList = array();
        $tagsIdNameDict = array();
        $tagsMeasureNameDict = array();
        $originUpperLimitDict = array();
        $originLowerLimitDict = array();
        $tagsDesc = array();
        foreach ($tags as $tag) {
            array_push($tagsNameList, $tag->tag_name);
            $tagsIdNameDict[$tag->tag_name] = $tag->id;
            $tagsMeasureNameDict[$tag->tag_name] = $tag->measure;
            $tagsDesc[$tag->tag_name] = $tag->alias ? $tag->alias : ($tag->description ? $tag->description : $tag->tag_name);
            $originUpperLimitDict[$tag->tag_name] = $tag->origin_upper_limit;
            $originLowerLimitDict[$tag->tag_name] = $tag->origin_lower_limit;
        }
        return [
            'tagsNameList' => $tagsNameList,
            'tagsIdNameDict' => $tagsIdNameDict,
            'tagsMeasureNameDict' => $tagsMeasureNameDict,
            'tagsDesc' => $tagsDesc,
            'originUpperLimitDict' => $originUpperLimitDict,
            'originLowerLimitDict' => $originLowerLimitDict,
        ];
    }

    private function getReturnFromHistorianResponse($datas, $tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict, $tagsDesc)
    {

        if ($datas['code'] === self::AJAX_SUCCESS && $datas['data']['ErrorCode'] === 0) {
            $datas = $datas['data']['Data'];
            array_walk($datas, function (&$data) use ($tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict, $tagsDesc) {
                $data['id'] = $tagsIdNameDict[$data['TagName']];
                $data['measure'] = $tagsMeasureNameDict[$data['TagName']];
                $data['origin_upper_limit'] = round($originUpperLimitDict[$data['TagName']], 0);
                $data['origin_lower_limit'] = round($originLowerLimitDict[$data['TagName']], 0);
                $data['description'] = $tagsDesc[$data['TagName']];
            });
            return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $datas));
        } else {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '获取失败', ['errorMessage' => $datas['data']['ErrorMessage']]));
        }
    }

    /**
     * @OA\POST(
     *     path="/api/historian-data/current-data",
     *     tags={"historian data api"},
     *     operationId="current-data",
     *     summary="获取 current data",
     *     description="使用说明：获取 current data",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="historian tag ids string，使用','分隔，example: tagIds=1,2,3",
     *         in="query",
     *         name="tagIds",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     )
     * )
     */
    public function currentData(Request $request)
    {
        $tagIds = $request->input('tagIds');
        if (!$tagIds) {
            return UtilService::format_data(self::AJAX_FAIL, '未提供tagIds', '');
        }
        $_ = $this->getTagList($tagIds);
        $tagsNameList = $_['tagsNameList'];
        $tagsIdNameDict = $_['tagsIdNameDict'];
        $tagsDesc = $_['tagsDesc'];
        $tagsMeasureNameDict = $_['tagsMeasureNameDict'];
        $originUpperLimitDict = $_['originUpperLimitDict'];
        $originLowerLimitDict = $_['originLowerLimitDict'];

        $tagsNameString = implode(';', $tagsNameList);
        if (!$tagsNameList) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, 'tagIds 错误，找不到对应tag', ''));
        }
        $datas = HistorianService::currentData($tagsNameString);

        return $this->getReturnFromHistorianResponse($datas, $tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict, $tagsDesc);
    }

    /**
     * @OA\POST(
     *     path="/api/historian-data/raw-data",
     *     tags={"historian data api"},
     *     operationId="raw-data",
     *     summary="获取原始数据 raw data",
     *     description="使用说明：获取原始数据 data",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="historian tag ids string，使用','分隔，example: tagIds=1,2,3",
     *         in="query",
     *         name="tagIds",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="起始时间 ISO8601 utc 时间字符串, example:2020-06-11T06:57:37.000Z",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="结束时间 ISO8601 utc 时间字符串, example:2020-06-11T06:57:37.000Z",
     *         in="query",
     *         name="end",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="每个tag的数据量，从开始时间取count条数据，默认为0，表示不限数量，以开始-结束时间为界。最大为5000。",
     *         in="query",
     *         name="count",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="0"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     )
     * )
     */
    public function rawData(Request $request)
    {
        $tagIds = $request->input('tagIds');
        $start = $request->input('start');
        $end = $request->input('end');
        $count = $request->input('count') ? $request->input('count') : 0;

        if (!$tagIds) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供tagIds', ''));
        }
        if (!$start) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供start', ''));
        }
        if (!$end) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供end', ''));
        }

        $_ = $this->getTagList($tagIds);
        $tagsNameList = $_['tagsNameList'];
        $tagsIdNameDict = $_['tagsIdNameDict'];
        $tagsDesc = $_['tagsDesc'];
        $tagsMeasureNameDict = $_['tagsMeasureNameDict'];
        $originUpperLimitDict = $_['originUpperLimitDict'];
        $originLowerLimitDict = $_['originLowerLimitDict'];
        $tagsNameString = implode(';', $tagsNameList);
        if (!$tagsNameList) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, 'tagIds 错误，找不到对应tag', ''));
        }
        $datas = HistorianService::rawData($tagsNameString, $start, $end, $count);

        return $this->getReturnFromHistorianResponse($datas, $tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict, $tagsDesc);
    }

    /**
     * @OA\POST(
     *     path="/api/historian-data/sampled-data",
     *     tags={"historian data api"},
     *     operationId="sampled-data",
     *     summary="获取 sampled data",
     *     description="使用说明：获取 sampled data, 默认按count计算时间间隔,间隔内取平均值,取count个历史数据",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="historian tag ids string，使用','分隔，example: tagIds=1,2,3",
     *         in="query",
     *         name="tagIds",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="起始时间 ISO8601 utc 时间字符串, example:2020-06-11T06:57:37.000Z",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="结束时间 ISO8601 utc 时间字符串, example:2020-06-11T06:57:37.000Z",
     *         in="query",
     *         name="end",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="详见 Historian 文档",
     *         in="query",
     *         name="count",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="100"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="详见 Historian 文档",
     *         in="query",
     *         name="samplingMode",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="2"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="详见 Historian 文档",
     *         in="query",
     *         name="calculationMode",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="1"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="详见 Historian 文档",
     *         in="query",
     *         name="intervalMS",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     )
     * )
     */
    public function sampledData(Request $request)
    {
        $tagIds = $request->input('tagIds');
        $start = $request->input('start');
        $end = $request->input('end');
        $count = $request->input('count') ? $request->input('count') : 0;
        $samplingMode = $request->input('samplingMode') ? $request->input('samplingMode') : 2;
        $calculationMode = $request->input('calculationMode') ? $request->input('calculationMode') : 1;
        $intervalMS = $request->input('intervalMS') ? $request->input('intervalMS') : null;

        if (!$tagIds) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供tagIds', ''));
        }
        if (!$start) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供start', ''));
        }
        if (!$end) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供end', ''));
        }

        $_ = $this->getTagList($tagIds);
        $tagsNameList = $_['tagsNameList'];
        $tagsIdNameDict = $_['tagsIdNameDict'];
        $tagsDesc = $_['tagsDesc'];
        $tagsMeasureNameDict = $_['tagsMeasureNameDict'];
        $originUpperLimitDict = $_['originUpperLimitDict'];
        $originLowerLimitDict = $_['originLowerLimitDict'];
        $tagsNameString = implode(';', $tagsNameList);
        if (!$tagsNameList) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, 'tagIds 错误，找不到对应tag', ''));
        }
        $datas = HistorianService::SampledData($tagsNameString, $start, $end, $count, $samplingMode, $calculationMode, $intervalMS);

        return $this->getReturnFromHistorianResponse($datas, $tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict, $tagsDesc);
    }

    /**
     * @OA\POST(
     *     path="/api/historian-data/watch-data",
     *     tags={"historian data api"},
     *     operationId="watch-data",
     *     summary="获取 watch data",
     *     description="使用说明：获取 watch data",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="historian tag ids string，使用','分隔，example: tagIds=1,2,3",
     *         in="query",
     *         name="tagIds",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="historian funcs string，使用','分隔",
     *         in="query",
     *         name="funcs",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     )
     * )
     */
    public function watchData(Request $request)
    {
        $tagIds = $request->input('tagIds');
        $funcs = $request->input('funcs');
        if (!$tagIds) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供tagIds', ''));
        }
        $_ = $this->getTagList($tagIds);
        $tagsNameList = $_['tagsNameList'];
        $tagsIdNameDict = $_['tagsIdNameDict'];
        $tagsMeasureNameDict = $_['tagsMeasureNameDict'];
        $originUpperLimitDict = $_['originUpperLimitDict'];
        $originLowerLimitDict = $_['originLowerLimitDict'];

        $tagsNameString = implode(';', $tagsNameList);
        if (!$tagsNameList) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, 'tagIds 错误，找不到对应tag', ''));
        }
        $datas = HistorianService::currentData($tagsNameString);
        $tagsData = $this->returnFromHistorianResponse($datas, $tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict);
        if($tagsData && count($tagsData) != 0) {
            foreach ($tagsData as $key => $tagsDatum) {
                $v = 0;
                if ($tagsDatum['ErrorCode'] == 0) {
                    $v = $tagsDatum['Samples'][0]['Value'];
                }
                $newData = array(
                    "type" => "single",
                    "id" => $tagsDatum['id'],
                    "TagName" => $tagsDatum['TagName'],
                    "measure" => $tagsDatum['measure'],
                    "origin_lower_limit" => $tagsDatum['origin_lower_limit'],
                    "origin_upper_limit" => $tagsDatum['origin_upper_limit'],
                    "value" => round($v, 2)
                );
                $tagsData[$key] = $newData;
            }

            //计算函数的值
            $data_funcs = array();
            if ($funcs) {
                if (strpos($funcs, ',') !== false) {
                    $fun_arr = explode(',', $funcs);
                } else {
                    $fun_arr = [$funcs];
                }

                //将最长tag放到最前面，先替换掉，不然长的tag有可能包含短的tag字符串，导致局部替换
                $tagsData = $this->sortArr($tagsData);
                foreach ($fun_arr as $func) {
                    $str = $func;
                    foreach ($tagsData as $item) {
                        if (strpos($str, $item['TagName']) !== false) {
                            //替換字符串為實際值
                            $str = str_replace($item['TagName'], $item['value'], $str);
                        }
                    }
                    $val = eval("return $str;");

                    $data_funcs[] = array(
                        "value" => $val ? round($val, 2) : 0,
                        "type" => "func",
                        "id" => $func
                    );
                }
            }

            $final = array_merge($tagsData, $data_funcs);
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $final);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    private function returnFromHistorianResponse($datas, $tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict)
    {

        if ($datas['code'] === self::AJAX_SUCCESS && $datas['data']['ErrorCode'] === 0) {
            $datas = $datas['data']['Data'];
            array_walk($datas, function (&$data) use ($tagsIdNameDict, $tagsMeasureNameDict, $originUpperLimitDict, $originLowerLimitDict) {
                $data['id'] = $tagsIdNameDict[$data['TagName']];
                $data['measure'] = $tagsMeasureNameDict[$data['TagName']];
                $data['origin_upper_limit'] = $originUpperLimitDict[$data['TagName']];
                $data['origin_lower_limit'] = $originLowerLimitDict[$data['TagName']];
            });
            return $datas;
        } else {
            return [];
        }
    }

    private function sortArr($arr){
        for($i=1;$i<count($arr);$i++){
            //内层循环参与比较的元素
            for($j=0;$j<count($arr)-1;$j++){
                //比较相邻的两个元素
                if(strlen($arr[$j]['TagName']) < strlen($arr[$j+1]['TagName'])){
                    $temp = $arr[$j];
                    $arr[$j] = $arr[$j+1];
                    $arr[$j+1] = $temp;
                }
            }
        }

        return $arr;
    }
}
