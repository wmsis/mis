<?php
/**
* 南瑞电表映射关系控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\ElectricityMap;
use Illuminate\Database\QueryException;
use UtilService;

class ElectricityMapController extends Controller
{
    /**
     * @OA\GET(
     *     path="/api/electricity-map/index",
     *     tags={"电表地址映射关系electricity-map"},
     *     operationId="electricity-map-index",
     *     summary="分页获取数据列表",
     *     description="使用说明：分页获取数据列表",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Parameter(
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="页数",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="关键字中文名搜索",
     *         in="query",
     *         name="cn_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="ElectricityMaps",
     *                  description="ElectricityMaps",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ElectricityMaps")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $orgnization_id = $request->input('orgnization_id');
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('cn_name');

        $rows = ElectricityMap::select(['*'])->where('orgnization_id', $orgnization_id);
        if ($name) {
            $rows = $rows->where('cn_name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\POST(
     *     path="/api/electricity-map/store",
     *     tags={"电表地址映射关系electricity-map"},
     *     operationId="electricity-map-store",
     *     summary="新增单条数据",
     *     description="使用说明：新增单条数据",
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
     *         description="中文名字",
     *         in="query",
     *         name="cn_name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="点位地址",
     *         in="query",
     *         name="addr",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="函数",
     *         in="query",
     *         name="func",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="ElectricityMap",
     *                  description="ElectricityMap",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ElectricityMap")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['addr', 'cn_name', 'func', 'orgnization_id']);
        try {
            $res = ElectricityMap::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
    }

    /**
     * @OA\GET(
     *     path="/api/electricity-map/show/{id}",
     *     tags={"电表地址映射关系electricity-map"},
     *     operationId="electricity-map-show",
     *     summary="获取详细信息",
     *     description="使用说明：获取详细信息",
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
     *         description="ElectricityMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="ElectricityMap",
     *                  description="ElectricityMap",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ElectricityMap")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $row = ElectricityMap::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $row);
    }

    /**
     * @OA\POST(
     *     path="/api/electricity-map/update/{id}",
     *     tags={"电表地址映射关系electricity-map"},
     *     operationId="electricity-map-update",
     *     summary="修改",
     *     description="使用说明：修改单条数据",
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
     *         description="ElectricityMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="中文名字",
     *         in="query",
     *         name="cn_name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="点位地址",
     *         in="query",
     *         name="addr",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="函数",
     *         in="query",
     *         name="func",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="ElectricityMap",
     *                  description="ElectricityMap",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/ElectricityMap")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = ElectricityMap::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该数据不存在', ''));
        }
        $input = $request->input();
        $allowField = ['addr', 'cn_name', 'func', 'orgnization_id'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $row[$field] = $inputValue;
            }
        }
        try {
            $row->save();
            $row->refresh();
        } catch (Exception $ex) {
            return UtilService::format_data(self::AJAX_FAIL, '修改失败', $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $row);
    }

    /**
     * @OA\DELETE(
     *     path="/api/electricity-map/destroy/{id}",
     *     tags={"电表地址映射关系electricity-map"},
     *     operationId="electricity-map-destroy",
     *     summary="删除单条数据",
     *     description="使用说明：删除单条数据",
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
     *         description="ElectricityMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="detroy succeed",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $row = ElectricityMap::find($id);
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


/**
 * @OA\Definition(
 *     definition="ElectricityMaps",
 *     type="array",
 *     @OA\Items(ref="#/definitions/ElectricityMap")
 * )
 */
