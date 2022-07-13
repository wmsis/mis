<?php
/**
* 设备备件控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Models\SIS\Equipment\Equipment;
use App\Http\Models\SIS\Equipment\EquipmentSparePart;
use App\Http\Requests\API\Equipment\StoreEquipmentSparePartRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use UtilService;

class EquipmentSparePartController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/spare-part/index",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 备件 列表",
     *     description="使用说明：获取 equipment 备件 列表",
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
     * @SWG\Parameter(
     *     description="name 搜索",
     *     in="query",
     *     name="name",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentSpareParts",
     *              description="EquipmentSpareParts",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentSpareParts")
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

        $name = $request->input('name');
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }

        $parts = $equipment->equipment_spare_parts();

        if ($name) {
            $parts = $parts->where('name', 'like', "%{$name}%");
        }

        $total = $parts->count();
        $parts = $parts->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $parts, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/spare-part/show/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 equipment 备件 详细信息",
     *     description="使用说明：获取 equipment 备件 详细信息",
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
     *     description="equipment spare part 主键",
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
     *              property="EquipmentSparePart",
     *              description="EquipmentSparePart",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentSparePart")
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
        $part = $equipment->equipment_spare_parts()->find($id);
        if (!$part) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该备件不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $part));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/spare-part/store",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="新增 equipment 备件",
     *     description="使用说明：新增 equipment 备件",
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
     *     description="equipment spare part 序列号",
     *     in="formData",
     *     name="serial_number",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 名称",
     *     in="formData",
     *     name="name",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 型号",
     *     in="formData",
     *     name="model",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 在装数量",
     *     in="formData",
     *     name="equipped_quantiry",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 生产厂家",
     *     in="formData",
     *     name="manufacturer",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 备注",
     *     in="formData",
     *     name="remark",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentSparePart",
     *              description="EquipmentSparePart",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentSparePart")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreEquipmentSparePartRequest $request, $equipment_id)
    {
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }

        $input = $request->only(['serial_number', 'name', 'model', 'equipped_quantity', 'remark']);
        try {
            $res = new EquipmentSparePart($input);
            $equipment->equipment_spare_parts()->save($res);
            $res->refresh();
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/spare-part/update/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="修改 equipment 备件",
     *     description="使用说明：修改 equipment 备件",
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
     *     description="equipment spare part 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 序列号",
     *     in="formData",
     *     name="serial_number",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 名称",
     *     in="formData",
     *     name="name",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 型号",
     *     in="formData",
     *     name="model",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 在装数量",
     *     in="formData",
     *     name="equipped_quantiry",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 生产厂家",
     *     in="formData",
     *     name="manufacturer",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment spare part 备注",
     *     in="formData",
     *     name="remark",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentSparePart",
     *              description="EquipmentSparePart",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentSparePart")
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
        $part = $equipment->equipment_spare_parts()->find($id);
        if (!$part) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该备件不存在', ''));
        }
        $input = $request->only(['serial_number', 'name', 'model', 'equipped_quantity', 'remark']);
        foreach ($input as $k => $v) {
            $part->$k = $v;
        }

        try {
            $part->save();
            $part->refresh();
        } catch (Exception $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $part));
    }

    /**
     * @SWG\DELETE(
     *     path="/api/equipment/{equipment_id}/spare-part/destroy/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="删除 equipment 备件",
     *     description="使用说明：删除 equipment 备件",
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
        $part = $equipment->equipment_spare_parts()->find($id);
        if (!$part) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该备件不存在', ''));
        }
        try {
            $part->delete();
        } catch (Exception $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }
}

/**
 * @SWG\Definition(
 *     definition="EquipmentSpareParts",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/EquipmentSparePart")
 * )
 */
