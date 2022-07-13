<?php
/**
* 设备控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API\Equipment;

use App\Http\Models\SIS\Equipment\Equipment;
use App\Http\Requests\API\Equipment\StoreEquipmentRequest;
use Illuminate\Database\QueryException;
use UtilService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

class EquipmentController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/equipment/index",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 设备 列表",
     *     description="使用说明：获取 设备 列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         type="integer",
     *         default=20,
     *     ),
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
     *              property="Equipments",
     *              description="Equipments",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/Equipments")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('name');

        $equipments = Equipment::select(['*']);
        if ($name) {
            $equipments = $equipments->where('name', 'like', "%{$name}%");
        }
        $total = $equipments->count();
        $equipments = $equipments->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $equipments, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/equipment/show/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="获取 设备 详细信息",
     *     description="使用说明：获取 设备 详细信息",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="equipment 主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="succeed",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="Equipment",
     *                  description="Equipment",
     *                  allOf={
     *                      @SWG\Schema(ref="#/definitions/Equipment")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $equipment));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/store",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="新增 equipment",
     *     description="使用说明：新增 equipment",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="equipment name",
     *     in="formData",
     *     name="name",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 型号",
     *     in="formData",
     *     name="model",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 厂家",
     *     in="formData",
     *     name="manufacturer",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 序列号",
     *     in="formData",
     *     name="serial_number",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 投产日期",
     *     in="formData",
     *     name="production_date",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="设备状态",
     *     in="formData",
     *     name="status",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="责任人姓名",
     *     in="formData",
     *     name="charge_person_name",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="责任人电话",
     *     in="formData",
     *     name="charge_person_phone",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="服役位置",
     *     in="formData",
     *     name="work_location",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="Equipment",
     *              description="Equipment",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/Equipment")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreEquipmentRequest $request)
    {
        $input = $request->only(['name', 'model', 'manufacturer', 'serial_number', 'production_date', 'status', 'charge_person_name', 'charge_person_phone', 'work_location']);
        try {
            $res = Equipment::create($input);
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/equipment/update/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="修改 equipment",
     *     description="使用说明：修改 equipment",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="equipment 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment name",
     *     in="formData",
     *     name="name",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 型号",
     *     in="formData",
     *     name="model",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 厂家",
     *     in="formData",
     *     name="manufacturer",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 序列号",
     *     in="formData",
     *     name="serial_number",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="equipment 投产日期",
     *     in="formData",
     *     name="production_date",
     *     required=false,
     *     type="string",
     * ),
     *       @SWG\Parameter(
     *     description="设备状态",
     *     in="formData",
     *     name="status",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="责任人姓名",
     *     in="formData",
     *     name="charge_person_name",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="责任人电话",
     *     in="formData",
     *     name="charge_person_phone",
     *     required=false,
     *     type="string",
     * ),
     *   @SWG\Parameter(
     *     description="服役位置",
     *     in="formData",
     *     name="work_location",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="Equipment",
     *              description="Equipment",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/Equipment")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function update(Request $request, $id)
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        $input = $request->input();
        $allowField = ['name', 'model', 'manufacturer', 'serial_number', 'production_date', 'status', 'charge_person_name', 'charge_person_phone', 'work_location'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $equipment[$field] = $inputValue;
            }
        }
        try {
            if(isset($input['name'])){
                $row = Equipment::where('name', $input['name'])->first();
                if($row && $row->id != $id){
                    return UtilService::format_data(self::AJAX_FAIL, '设备名称重复', '');
                }
            }
            $equipment->save();
            $equipment->refresh();
        } catch (Exception $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', $ex->getMessage()));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $equipment));
    }

    /**
     * @SWG\DELETE(
     *     path="/api/equipment/destroy/{id}",
     *     tags={"equipment api"},
     *     operationId="",
     *     summary="删除 equipment",
     *     description="使用说明：删除 equipment",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="equipment 主键",
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
    public function destroy($id)
    {
        $equipment = Equipment::find($id);
        if (!$equipment) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该设备不存在', ''));
        }
        try {
            $equipment->delete();
        } catch (Exception $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }

}

/**
 * @SWG\Definition(
 *     definition="Equipments",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Equipment")
 * )
 */
