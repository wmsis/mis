<?php
/**
* 设备更改记录控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Models\SIS\Equipment\Equipment;
use App\Http\Models\SIS\Equipment\EquipmentChangeRecord;
use App\Http\Requests\API\Equipment\StoreEquipmentChangeRecordRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use UtilService;

class EquipmentChangeRecordController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/change-record/index",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 更改记录 列表",
     *     description="使用说明：获取 equipment 更改记录 列表",
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
     *              property="EquipmentChangeRecords",
     *              description="EquipmentChangeRecords",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentChangeRecords")
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
        $records = $equipment->equipment_change_records();

        $total = $records->count();
        $records = $records->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $records, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/change-record/show/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 更改记录 详细信息",
     *     description="使用说明：获取 equipment 更改记录 详细信息",
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
     *     description="equipment change record 主键",
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
     *              property="EquipmentChangeRecord",
     *              description="EquipmentChangeRecord",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentChangeRecord")
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
        $record = $equipment->equipment_change_records()->find($id);
        if (!$record) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该更改记录不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $record));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/change-record/store",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="新增 equipment 更改记录",
     *     description="使用说明：新增 equipment 更改记录",
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
     *     description="equipment change record date",
     *     in="formData",
     *     name="date",
     *     required=true,
     *     type="string",
     *     format="date",
     * ),
     * @SWG\Parameter(
     *     description="equipment change record 内容",
     *     in="formData",
     *     name="record",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentChangeRecord",
     *              description="EquipmentChangeRecord",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentChangeRecord")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreEquipmentChangeRecordRequest $request, $equipment_id)
    {
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        $input = $request->only(['date', 'record']);
        try {
            $res = new EquipmentChangeRecord($input);
            $equipment->equipment_change_records()->save($res);
            $res->refresh();
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/change-record/update/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="修改 equipment 更改记录",
     *     description="使用说明：修改 equipment 更改记录",
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
     *     description="equipment change record 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment change record date",
     *     in="formData",
     *     name="date",
     *     required=false,
     *     type="string",
     *     format="date",
     * ),
     * @SWG\Parameter(
     *     description="equipment change record 内容",
     *     in="formData",
     *     name="record",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentChangeRecord",
     *              description="EquipmentChangeRecord",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentChangeRecord")
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
        $record = $equipment->equipment_change_records()->find($id);
        if (!$record) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该更改记录不存在', ''));
        }
        $input = $request->only(['date', 'record']);
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
     *     path="/api/equipment/{equipment_id}/change-record/destroy/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="删除 equipment 更改记录",
     *     description="使用说明：删除 equipment 更改记录",
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
     *     description="equipment change record 主键",
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
        $record = $equipment->equipment_change_records()->find($id);
        if (!$record) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该更改记录不存在', ''));
        }
        try {
            $record->delete();
        } catch (Exception $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }
}

/**
 * @SWG\Definition(
 *     definition="EquipmentChangeRecords",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/EquipmentChangeRecord")
 * )
 */
