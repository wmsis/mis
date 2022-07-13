<?php
/**
* 设备维修控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Models\SIS\Equipment\Equipment;
use App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord;
use App\Http\Requests\API\Equipment\StoreEquipmentMaintenanceRecordRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use UtilService;
use Log;

class EquipmentMaintenanceRecordController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/maintenance-record/index",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 检修记录 列表",
     *     description="使用说明：获取 equipment 检修记录 列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *     description="equipment id",
     *     in="path",
     *     name="equipment_id",
     *     required=true,
     *     type="string",
     * ),
     *     @SWG\Parameter(
     *     description="每页数据量",
     *     in="query",
     *     name="num",
     *     required=false,
     *     type="integer",
     *     default=20,
     * ),
     * @SWG\Parameter(
     *     description="页数",
     *     in="query",
     *     name="page",
     *     required=false,
     *     type="integer",
     *     default=1,
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentMaintenanceRecords",
     *              description="EquipmentMaintenanceRecords",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentMaintenanceRecords")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function index(Request $request, $equipment_id)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        $records = $equipment->equipment_maintenance_records();

        $total = $records->count();
        $records = $records->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $records, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/maintenance-record/show/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 检修记录 详细信息",
     *     description="使用说明：获取 equipment 检修记录 详细信息",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="equipment id",
     *     in="path",
     *     name="equipment_id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment maintenance record 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="integer",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentMaintenanceRecord",
     *              description="EquipmentMaintenanceRecord",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentMaintenanceRecord")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function show($equipment_id, $id)
    {
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        $record = $equipment->equipment_maintenance_records()->find($id);
        if (!$record) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该检修记录不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $record));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/maintenance-record/store",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="新增 equipment 检修记录",
     *     description="使用说明：新增 equipment 检修记录",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *     description="equipment id",
     *     in="path",
     *     name="equipment_id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修日期",
     *     in="formData",
     *     name="date",
     *     required=true,
     *     type="string",
     *     format="date",
     * ),
     * @SWG\Parameter(
     *     description="检修性质",
     *     in="formData",
     *     name="kind",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="负责人",
     *     in="formData",
     *     name="supervisor",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="验收评价",
     *     in="formData",
     *     name="evaluation",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="工作组成员",
     *     in="formData",
     *     name="members",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修前状况",
     *     in="formData",
     *     name="prev_status",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修内容",
     *     in="formData",
     *     name="maintenance_content",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="试运情况",
     *     in="formData",
     *     name="test_status",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修后状况",
     *     in="formData",
     *     name="after_status",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="记录人",
     *     in="formData",
     *     name="recorder",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="记录时间",
     *     in="formData",
     *     name="record_time",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentMaintenanceRecord",
     *              description="EquipmentMaintenanceRecord",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentMaintenanceRecord")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreEquipmentMaintenanceRecordRequest $request, $equipment_id)
    {
        $input = $request->only(['date', 'kind', 'supervisor', 'evaluation', 'members', 'prev_status', 'maintenance_content',
            'test_status', 'after_status', 'recorder', 'record_time']);

        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }

        try {
            $res = new EquipmentMaintenanceRecord($input);
            $equipment->equipment_maintenance_records()->save($res);
            $res->refresh();
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/maintenance-record/update/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="修改 equipment 检修记录",
     *     description="使用说明：修改 equipment 检修记录",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *     description="equipment id",
     *     in="path",
     *     name="equipment_id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment maintenance record 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修日期",
     *     in="formData",
     *     name="date",
     *     required=false,
     *     type="string",
     *     format="date",
     * ),
     * @SWG\Parameter(
     *     description="检修性质",
     *     in="formData",
     *     name="kind",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="负责人",
     *     in="formData",
     *     name="supervisor",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="验收评价",
     *     in="formData",
     *     name="evaluation",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="工作组成员",
     *     in="formData",
     *     name="members",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修前状况",
     *     in="formData",
     *     name="prev_status",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修内容",
     *     in="formData",
     *     name="maintenance_content",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="试运情况",
     *     in="formData",
     *     name="test_status",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="检修后状况",
     *     in="formData",
     *     name="after_status",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="记录人",
     *     in="formData",
     *     name="recorder",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="记录时间",
     *     in="formData",
     *     name="record_time",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentMaintenanceRecord",
     *              description="EquipmentMaintenanceRecord",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentMaintenanceRecord")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function update(Request $request, $equipment_id, $id)
    {
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }

        $record = $equipment->equipment_maintenance_records()->find($id);
        if (!$record) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该检修记录不存在', ''));
        }
        $input = $request->only(['date', 'kind', 'supervisor', 'evaluation', 'members', 'prev_status', 'maintenance_content',
            'test_status', 'after_status', 'recorder', 'record_time']);
        foreach ($input as $k => $v) {
            $record->$k = $v;
        }
        try {
            $record->save();
            $record->refresh();
        } catch (Exception $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $record));
    }

    /**
     * @SWG\DELETE(
     *     path="/api/equipment/{equipment_id}/maintenance-record/destroy/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="删除 equipment 检修记录",
     *     description="使用说明：删除 equipment 检修记录",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *     description="equipment id",
     *     in="path",
     *     name="equipment_id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment maintenance record 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="detroy succeed",
     * ),
     * )
     */
    public function destroy($equipment_id, $id)
    {
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }

        $record = $equipment->equipment_maintenance_records()->find($id);
        if (!$record) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该检修记录不存在', ''));
        }
        try {
            $record->delete();
        } catch (Exception $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }

    /**
     * @SWG\GET(
     *     path="/api/equipment/maintenance-record/gragh",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 所有检修记录图表",
     *     description="使用说明：获取 equipment 所有检修记录图表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="统计区间",
     *         in="query",
     *         name="period",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     * ),
     * )
     */
    public function gragh(Request $request)
    {
        $period = $request->input('period');
        if($period == 'month'){
            //最近12个月数据
            $start = date('Y-m', time() - 365 * 24 * 60 * 60);
            $end = date('Y-m');

            $year = (int)date('Y');
            $month = (int)date('m');
            $start = $start.'-01';
            $end = $end.'-'.$this->monthLastDay($year, $month);

            $year_num = (int)date('Y');
            $month_num = (int)date('m');
            $keylist = array(
                $this->countMonth($year_num, $month_num, 11) => 0,
                $this->countMonth($year_num, $month_num, 10) => 0,
                $this->countMonth($year_num, $month_num, 9) => 0,
                $this->countMonth($year_num, $month_num, 8) => 0,
                $this->countMonth($year_num, $month_num, 7) => 0,
                $this->countMonth($year_num, $month_num, 6) => 0,
                $this->countMonth($year_num, $month_num, 5) => 0,
                $this->countMonth($year_num, $month_num, 4) => 0,
                $this->countMonth($year_num, $month_num, 3) => 0,
                $this->countMonth($year_num, $month_num, 2) => 0,
                $this->countMonth($year_num, $month_num, 1) => 0,
                date('Y-m', time()) => 0
            );
        }
        else{
            //10年数据
            $start = date('Y', time() - 10 * 365 * 24 * 60 * 60);
            $end = date('Y');
            $start = $start.'-01-01';
            $end = $end.'-12-31';
            $year_num = (int)date('Y');

            $keylist = array(
                ($year_num-9) => 0,
                ($year_num-8) => 0,
                ($year_num-7) => 0,
                ($year_num-6) => 0,
                ($year_num-5) => 0,
                ($year_num-4) => 0,
                ($year_num-3) => 0,
                ($year_num-2) => 0,
                ($year_num-1) => 0,
                $year_num => 0
            );
        }

        $records = EquipmentMaintenanceRecord::all();
        if($records){
            $equip_arr = array();
            foreach ($records as $key=>$item){
                $equipment = $item->equipment()->withTrashed()->first();
                $records[$key]['equipment'] = $equipment;
                $equip_arr[$equipment->name][] = $records[$key];
            }

            $statistic = array();
            foreach ($equip_arr as $key=>$item_arr){
                $statistic[$key]['pie'] = array(
                    "general" => 0,
                    "worse" => 0,
                    "serious" => 0
                );
                $statistic[$key]['column'] = array(
                    "general" => $keylist,
                    "worse" => $keylist,
                    "serious" => $keylist
                );

                foreach ($item_arr as $item){
                    //日期
                    if($period == 'month') {
                        $k = substr($item->date, 0, 7);
                    }
                    else{
                        $k = substr($item->date, 0, 4);
                    }

                    if($item->kind == 'general')
                    {
                        //pie
                        $statistic[$key]['pie']['general']++;

                        //column
                        foreach($statistic[$key]['column']['general'] as $date=>$val){
                            if ($date == $k) {
                                $statistic[$key]['column']['general'][$date]++;
                            }
                        }
                    }
                    elseif($item->kind == 'worse'){
                        //pie
                        $statistic[$key]['pie']['worse']++;

                        //column
                        foreach($statistic[$key]['column']['worse'] as $date=>$val){
                            if ($date == $k) {
                                $statistic[$key]['column']['worse'][$date]++;
                            }
                        }
                    }
                    else{
                        //pie
                        $statistic[$key]['pie']['serious']++;

                        //column
                        foreach($statistic[$key]['column']['serious'] as $date=>$val){
                            if ($date == $k) {
                                $statistic[$key]['column']['serious'][$date]++;
                            }
                        }
                    }
                }
            }

            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $statistic);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    private function monthLastDay($year, $month){
        if($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12){
            return 31;
        }
        elseif($month == 4 || $month == 6 || $month == 9 || $month == 11){
            return 30;
        }
        elseif($year%400 == 0 || ($year%4==0 && $year%100!=0)){
            return 29;
        }
        else{
            return 28;
        }
    }

    private function countMonth($year, $month, $num){
        $diff = $month - $num;
        if($diff > 0){
            $computerMonth = $diff;
            $computerYear = $year;
        }
        elseif($diff == 0){
            $computerMonth = 12;
            $computerYear = $year - 1;
        }
        else{
            $computerYear = $year - 1;
            $computerMonth = 12 + $diff;
        }

        if($computerMonth < 10){
            $computerMonth = '0'.$computerMonth;
        }
        $res = $computerYear.'-'.$computerMonth;
        return $res;
    }
}

/**
 * @SWG\Definition(
 *     definition="EquipmentMaintenanceRecords",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/EquipmentMaintenanceRecord")
 * )
 */
