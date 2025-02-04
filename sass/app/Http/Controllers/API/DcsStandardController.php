<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\DcsGroup;
use App\Models\SIS\DcsMap;
use App\Models\Mongo\HistorianData;
use App\Models\Mongo\HistorianFormatData;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Log;

class DcsStandardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dcs-standard/lists",
     *     tags={"DCS标准命名dcs-standard"},
     *     operationId="dcs-standard-lists",
     *     summary="获取所有名称列表",
     *     description="使用说明：获取所有名称列表",
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
     *         description="TAG关键字搜索",
     *         in="query",
     *         name="cn_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="分组名搜索",
     *         in="query",
     *         name="group_name",
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
     *                  @OA\Items(ref="#/components/schemas/DcsStandard")
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
    public function lists(Request $request)
    {
        $cn_name = $request->input('cn_name');
        $group_name = $request->input('group_name');
        $lists = DB::table('dcs_standard')
            ->leftJoin('dcs_group', 'dcs_standard.dcs_group_id', '=', 'dcs_group.id')
            ->leftJoin('dcs_map', 'dcs_standard.id', '=', 'dcs_map.dcs_standard_id')
            ->select('dcs_standard.*', 'dcs_group.name AS group_name')
            ->where('dcs_standard.type', 'dcs')
            ->where('dcs_map.orgnization_id', $this->orgnization->id)//配置过映射关系的
            ->orderBy('dcs_standard.sort', 'ASC')
            ->orderBy('dcs_standard.id', 'ASC');

        if($cn_name){
            $lists = $lists->where('dcs_standard.cn_name', 'like', "%{$cn_name}%");
        }

        if($group_name){
            $lists = $lists->where('dcs_group.name', 'like', "%{$group_name}%");
        }
        $lists = $lists->whereNull('dcs_standard.deleted_at')->whereNull('dcs_map.deleted_at')->whereNull('dcs_group.deleted_at')->get();

        //格式化数据
        $key_values = [];
        $other = [];
        $other['name'] = '未分组';
        $other['datalist'] = [];
        foreach ($lists as $key => $item) {
            if($item->dcs_group_id){
                $key_values[$item->dcs_group_id]['name'] = $item->group_name;
                //$key_values[$item->dcs_group_id]['dcs_group_id'] = $item->dcs_group_id;
                $key_values[$item->dcs_group_id]['datalist'][] = $item;
            }
            else{
                $other['datalist'][] = $item;
            }
        }

        $final = [];
        foreach ($key_values as $key => $item) {
            $final[] = $item;
        }

        if(!empty($other['datalist'])){
            $final[] = $other;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-standard/datalists",
     *     tags={"DCS标准命名dcs-standard"},
     *     operationId="dcs-standard-datalists",
     *     summary="获取所有数据列表",
     *     description="使用说明：获取所有数据列表",
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
     *         description="标准DCS名称ID列表，多个英文逗号隔开",
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
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function datalists(Request $request)
    {
        $ids = $request->input('ids');
        $start = $request->input('start');
        $end = $request->input('end');
        $id_arr = explode(',', $ids);
        $lists = DcsStandard::whereIn('id', $id_arr)->get();
        if($lists && count($lists) > 0){
            $local_data_table = 'historian_format_data_' . $this->orgnization->code;
            $obj_hitorian_format_local = (new HistorianFormatData())->setConnection($this->mongo_conn)->setTable($local_data_table);
            foreach ($lists as $key => $item) {
                $group = DcsGroup::find($item->dcs_group_id);
                $lists[$key]['group_name'] = $group && $group->name ? $group->name : '';

                $datalist = $obj_hitorian_format_local->select(['value', 'datetime'])
                    ->where('dcs_standard_id', $item->id)
                    ->orderBy('datetime', 'ASC')
                    ->where('datetime', '>=', $start)
                    ->where('datetime', '<=', $end)
                    ->get();

                $key_values = [];
                foreach ($datalist as $k9 => $data) {
                    $short_datetime = substr($data->datetime, 11, 5);
                    if(strpos($data->value, '.') !== false){
                        $key_values[$short_datetime] = round($data->value);
                    }
                    else{
                        $key_values[$short_datetime] = $data->value;
                    }
                }

                $lists[$key]['datalist'] = $key_values;
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-standard/currentdata",
     *     tags={"DCS标准命名dcs-standard"},
     *     operationId="dcs-standard-currentdata",
     *     summary="获取配置文件的最新数据列表",
     *     description="使用说明：获取配置文件的最新数据列表",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function currentdata(Request $request)
    {
        $datalist = [];
        $ids = config('blackbox.ids');;
        $id_arr = explode(',', $ids);
        $lists = DcsStandard::whereIn('id', $id_arr)->get();
        if($lists && count($lists) > 0){
            $local_data_table = 'historian_format_data_' . $this->orgnization->code;
            $obj_hitorian_format_local = (new HistorianFormatData())->setConnection($this->mongo_conn)->setTable($local_data_table);
            foreach ($lists as $key => $item) {
                $data = $obj_hitorian_format_local->select("*")
                    ->where('dcs_standard_id', $item->id)
                    ->orderBy('datetime', 'DESC')
                    ->first();

                if($data){
                    if(strpos($data->value, '.') !== false){
                        $currentValue = round($data->value);
                    }
                    else{
                        $currentValue = $data->value;
                    }

                    $datalist[] = array(
                        "value"=>$currentValue,
                        "cn_name"=>$item->cn_name,
                        "en_name"=>$item->en_name,
                        "messure"=>$item->messure
                    );
                }
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $datalist);
    }
}
