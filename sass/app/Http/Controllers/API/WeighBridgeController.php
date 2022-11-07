<?php
/**
* 地磅数据上报控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\WeighBridge;
use App\Models\SIS\WeighBridgeFormat;
use App\Models\SIS\WeighbridgeCateSmall;
use App\Models\SIS\WeighbridgeCateBig;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use UtilService;
use Log;

class WeighBridgeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/weighbridge/categories",
     *     tags={"地磅上报数据weighbridge"},
     *     operationId="weighbridge-categories",
     *     summary="获取所有垃圾分类列表",
     *     description="使用说明：获取所有垃圾分类列表",
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
     *         description="关键字搜索",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\JsonContent(
     *             @OA\Property(
	 *              	property="code",
	 *                  description="错误代码，0：为没有错误",
	 *                  type="integer",
	 *					default="0"
	 *             ),
     *             @OA\Property(
	 *                  property="data",
	 *                  description="返回数据",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/WeighBridgeFormat")
     *             ),
     *             @OA\Property(
	 *              	property="message",
	 *                  description="错误消息",
	 *                  type="string"
	 *             )
     *         ),
     *     ),
     * )
     */
    public function categories(Request $request)
    {
        $name = $request->input('name');
        if($name){
            $lists = WeighbridgeCateBig::where('name', 'like', "%{$name}%")->get();
        }
        else{
            $lists = WeighbridgeCateBig::all();
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }

    /**
     * @OA\Get(
     *     path="/api/weighbridge/datalists",
     *     tags={"地磅上报数据weighbridge"},
     *     operationId="weighbridge-datalists",
     *     summary="获取地磅数据列表",
     *     description="使用说明：获取地磅数据列表",
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
     *         description="大类垃圾名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="succeed",
     *      ),
     * )
     */
    public function datalists(Request $request)
    {
        $ids = $request->input('ids');
        $start = $request->input('start');
        $end = $request->input('end');
        $id_arr = explode(',', $ids);
        $lists = WeighbridgeCateBig::whereIn('id', $id_arr)->get();
        if($lists && count($lists) > 0){
            $table = 'weighbridge_format_' . $this->orgnization->code;
            $obj = (new WeighBridgeFormat())->setTable($table);
            foreach ($lists as $k1 => $item) {
                $small_ids = [];
                $small_names = $item->small_names;
                foreach ($small_names as $k2 => $small) {
                    $small_ids[] = $small->id;
                }

                $datalist = $obj->select(['net as value', 'taredatetime as datetime', 'weighbridge_cate_small_id'])
                    ->whereIn('weighbridge_cate_small_id', $small_ids)
                    ->where('taredatetime', '>', $start)
                    ->where('taredatetime', '<', $end)
                    ->orderBy('taredatetime', 'ASC')
                    ->get();

                foreach ($datalist as $k3 => $data) {
                    $small = WeighbridgeCateSmall::find($data->weighbridge_cate_small_id);
                    $datalist[$k3]['product'] = $small->name;
                }

                $key_values = [];
                foreach ($datalist as $k9 => $data) {
                    $short_datetime = substr($data->datetime, 11, 5);
                    $key_values[$short_datetime] = $data->value;
                }

                $lists[$k1]['datalist'] = $key_values;
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }

}
