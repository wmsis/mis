<?php
/**
* 设备参数控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Models\SIS\Equipment\Equipment;
use App\Http\Models\SIS\Equipment\EquipmentParam;
use App\Http\Requests\API\Equipment\StoreEquipmentParamRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use UtilService;

class EquipmentParamController extends Controller
{

    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/param/index",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 设备参数 列表",
     *     description="使用说明：获取 设备参数 列表",
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
     *              property="EquipmentParams",
     *              description="EquipmentParams",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentParams")
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
        $params = $equipment->equipment_params();

        if ($name) {
            $params = $params->where('name', 'like', "%{$name}%");
        }
        $total = $params->count();
        $params = $params->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($params as $key => $value) {
            $params[$key]['equipment_name'] = $equipment->name;
            $params[$key]['equipment_model'] = $equipment->model;
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $params, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/equipment/{equipment_id}/param/show/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 设备参数 详细信息",
     *     description="使用说明：获取 设备参数 详细信息",
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
     *     description="equipment param 主键",
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
     *              property="EquipmentParam",
     *              description="EquipmentParam",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentParam")
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
        $param = $equipment->equipment_params()->find($id);
        if (!$param) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该参数不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $param));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/param/store",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="新增 equipment param",
     *     description="使用说明：新增 equipment param",
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
     *     description="equipment param name",
     *     in="formData",
     *     name="name",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment param value",
     *     in="formData",
     *     name="value",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentParam",
     *              description="EquipmentParam",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentParam")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreEquipmentParamRequest $request, $equipment_id)
    {
        $equipment = Equipment::find($equipment_id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        $input = $request->only(['name', 'value']);
        try {
            $res = new EquipmentParam($input);
            $equipment->equipment_params()->save($res);
            $res->refresh();
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/{equipment_id}/param/update/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="修改 equipment param",
     *     description="使用说明：修改 equipment param",
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
     *     description="equipment param 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment param name",
     *     in="formData",
     *     name="name",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment param value",
     *     in="formData",
     *     name="value",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment id",
     *     in="formData",
     *     name="equipment_id",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="EquipmentParam",
     *              description="EquipmentParam",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/EquipmentParam")
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
        $param = $equipment->equipment_params()->find($id);
        if (!$param) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该参数不存在', ''));
        }
        $input = $request->only(['name', 'value']);
        foreach ($input as $k => $v) {
            $param->$k = $v;
        }
        try {
            $param->save();
            $param->refresh();
        } catch (Exception $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $param));
    }

    /**
     * @SWG\DELETE(
     *     path="/api/equipment/{equipment_id}/param/destroy/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="删除 equipment param",
     *     description="使用说明：删除 equipment param",
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
     *     description="equipment param 主键",
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
        $param = $equipment->equipment_params()->find($id);
        if (!$param) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该参数不存在', ''));
        }
        try {
            $param->delete();
        } catch (Exception $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }
}

/**
 * @SWG\Definition(
 *     definition="EquipmentParams",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/EquipmentParam")
 * )
 */
