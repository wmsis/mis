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
use App\Http\Models\SIS\Electricity;
use App\Http\Requests\API\StoreElectricityRequest;
use Log;

class ElectricityController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/electricity/index",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="获取电表数据列表",
     *     description="使用说明：获取电表数据列表",
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
     *      @SWG\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          required=false,
     *          type="integer",
     *          default=1,
     *      ),
     *      @SWG\Parameter(
     *          description="中文名称搜索",
     *          in="query",
     *          name="cn_name",
     *          required=false,
     *          type="string",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="succeed",
     *          @SWG\Schema(
     *               @SWG\Property(
     *                   property="Electricities",
     *                   description="Electricities",
     *                   allOf={
     *                       @SWG\Schema(ref="#/definitions/Electricity")
     *                   }
     *                )
     *           )
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $cn_name = $request->input('cn_name');

        $rows = Electricity::select(['*']);
        if ($cn_name) {
            $rows = $rows->where('cn_name', 'like', "%{$cn_name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @SWG\GET(
     *     path="/api/electricity/show/{id}",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="获取电表数据详细信息",
     *     description="使用说明：获取电表数据详细信息",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="主键",
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
     *                  property="Electricity",
     *                  description="Electricity",
     *                  allOf={
     *                      @SWG\Schema(ref="#/definitions/Electricity")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $row = Electricity::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '数据不存在', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $row);
    }

    /**
     * @SWG\POST(
     *     path="/api/electricity/store",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="新增",
     *     description="使用说明：新增",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="地址",
     *         in="formData",
     *         name="address",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="远动原始值",
     *         in="formData",
     *         name="value",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="实际值",
     *         in="formData",
     *         name="actual_value",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="品质描述",
     *         in="formData",
     *         name="quality",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="系数",
     *         in="formData",
     *         name="factor",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="中文名称",
     *         in="formData",
     *         name="cn_name",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="store succeed",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="Electricity",
     *                  description="Electricity",
     *                  allOf={
     *                      @SWG\Schema(ref="#/definitions/Electricity")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function store(StoreElectricityRequest $request)
    {
        $input = $request->only(['address', 'value', 'actual_value', 'quality', 'factor', 'cn_name']);
        try {
            $res = Electricity::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
    }

    /**
     * @SWG\POST(
     *     path="/api/electricity/store_multi",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="批量新增",
     *     description="使用说明：批量新增",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="input",
     *         in="formData",
     *         name="input",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function store_multi(Request $request)
    {
        $params = $request->only(['input']);
        if (!isset($params['input']) || !$params['input']) {
            return UtilService::format_data(self::AJAX_FAIL, '参数错误', '');
        }
        else{
            $datalist = json_decode($params['input'], true);
            foreach ($datalist as $key => $value) {
                $datalist[$key]['created_at'] = date('Y-m-d H:i:s');
                $datalist[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            try {
                $res = Electricity::insert($datalist);
            } catch (QueryException $e) {
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
    }

    /**
     * @SWG\DELETE(
     *     path="/api/electricity/destroy/{id}",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="删除",
     *     description="使用说明：删除electricity",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="detroy succeed",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $row = Electricity::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        try {
            $row->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '删除失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '删除成功', '');
    }

}
