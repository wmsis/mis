<?php
/**
* IEC104取得的电表数据控制器
*
* @author      叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\PowerDayData;
use App\Models\SIS\PowerMap;
use Illuminate\Database\QueryException;
use Log;

class ElectricityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/electricity/categories",
     *     tags={"南瑞电表数据electricity"},
     *     operationId="electricity-categories",
     *     summary="获取所有电表分类列表",
     *     description="使用说明：获取所有电表分类列表",
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
     *     ),
     * )
     */
    public function categories(Request $request)
    {
        $name = $request->input('name');
        if($name){
            $lists = DcsStandard::where('cn_name', 'like', "%{$name}%")->where('type', 'electricity')->orderBy('sort', 'ASC')->get();
        }
        else{
            $lists = DcsStandard::where('type', 'electricity')->orderBy('sort', 'ASC')->get();
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }

    /**
     * @OA\Get(
     *     path="/api/electricity/datalists",
     *     tags={"南瑞电表数据electricity"},
     *     operationId="electricity-datalists",
     *     summary="获取电表数据列表",
     *     description="使用说明：获取电表数据列表",
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
     *         description="电表名称ID列表，多个英文逗号隔开",
     *         in="query",
     *         name="ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始日期",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束日期",
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
        $lists = PowerMap::select(['dcs_standard_id', 'id'])->whereIn('dcs_standard_id', $id_arr)->get();
        if($lists && count($lists) > 0){
            $key_values = [];
            $begin_timestamp = strtotime($start);
            $end_timestamp = strtotime($end);
            for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                $date = date('Y-m-d', $i);
                $key_values[$date] = 0; //初始值
            }

            $table = 'power_day_data_' . $this->orgnization->code;
            $obj = (new PowerDayData())->setTable($table);
            foreach ($lists as $key => $item) {
                $dcs_standard = DcsStandard::find($item->dcs_standard_id);
                $datalist = $obj->select(['value', 'date'])
                    ->where('power_map_id', $item->id)
                    ->where('date', '>=', $start)
                    ->where('date', '<=', $end)
                    ->get();

                $lists[$key]['name'] = $dcs_standard ? $dcs_standard->cn_name : '';
                $lists[$key]['messure'] = $dcs_standard ? $dcs_standard->messure : '';

                foreach ($datalist as $k9 => $data) {
                    $key_values[$data->date] = (float)$data->value;
                }
                $lists[$key]['datalist'] = $key_values;
                unset($lists[$key]->id);
                unset($lists[$key]->dcs_standard_id);
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
    }
}
