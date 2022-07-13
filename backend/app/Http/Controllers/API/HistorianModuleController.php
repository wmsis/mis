<?php
/**
* tag模块控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Models\SIS\HistorianModule;
use App\Http\Models\SIS\HistorianTag;
use App\Http\Requests\API\StoreHistorianModuleRequest;
use Illuminate\Database\QueryException;
use UtilService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Log;

class HistorianModuleController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/historian-module/index",
     *     tags={"historian module api"},
     *     operationId="",
     *     summary="获取 module 列表",
     *     description="使用说明：获取 module 列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
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
     *              property="historianModules",
     *              description="historianModules",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianModules")
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

        $tags = HistorianModule::select(['*']);
        if ($name) {
            $tags = $tags->where('name', 'like', "%{$name}%");
        }
        $total = $tags->count();
        $tags = $tags->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($tags as $key=>$item){
            if(strpos($item->name, '汽机') !== false || strpos($item->name, '汽轮机') !== false){
                $tags[$key]->type = 'turbine';
            }
            elseif(strpos($item->name, '锅炉') !== false){
                $tags[$key]->type = 'boiler';
            }
            elseif(strpos($item->name, '公用') !== false){
                $tags[$key]->type = 'common';
            }
            else{
                $tags[$key]->type = '';
            }
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $tags, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/historian-module/show/{id}",
     *     tags={"historian module api"},
     *     operationId="",
     *     summary="获取 module 详细信息",
     *     description="使用说明：获取 module 详细信息",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="module 主键",
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
     *              property="historianModule",
     *              description="historianModule",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianModule")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function show($id)
    {
        $module = HistorianModule::find($id);
        if (!$module) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $module));
    }

    /**
     * @SWG\POST(
     *     path="/api/historian-module/store",
     *     tags={"historian module api"},
     *     operationId="",
     *     summary="新增 module",
     *     description="使用说明：新增 module",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="module name",
     *     in="formData",
     *     name="name",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="module description",
     *     in="formData",
     *     name="description",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianModule",
     *              description="historianModule",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianModule")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreHistorianModuleRequest $request)
    {
        $input = $request->only(['name', 'description']);
        DB::beginTransaction();
        try {
            $res = HistorianModule::create($input);
            $tag = new HistorianTag();
            $tag->tag_id = "fake-historian-tag-0000";
            $tag->tag_name = "fake-historian-tag";
            $tag->description = "初始化";
            $tag->historian_module_id = $res->id;
            $tag->save();

            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', $e->getMessage()));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/historian-module/update/{id}",
     *     tags={"historian module api"},
     *     operationId="",
     *     summary="修改 module",
     *     description="使用说明：修改 module",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="module 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="module name",
     *     in="formData",
     *     name="name",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="module description",
     *     in="formData",
     *     name="description",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     * ),
     * )
     */
    public function update(Request $request, $id)
    {
        $module = HistorianModule::find($id);
        if (!$module) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Module不存在', ''));
        }
        if ($name = $request->input('name')) {
            $module->name = $name;
        }
        if ($description = $request->input('description')) {
            $module->description = $description;
        }
        try {
            $module->save();
        } catch (Exception $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', ''));
    }

    /**
     * @SWG\DELETE(
     *     path="/api/historian-module/destroy/{id}",
     *     tags={"historian module api"},
     *     operationId="",
     *     summary="删除 module",
     *     description="使用说明：删除 module",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="module 主键",
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
        $module = HistorianModule::find($id);
        if (!$module) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        try {
            $module->delete();
        } catch (Exception $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }
}

/**
 * @SWG\Definition(
 *     definition="HistorianModules",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/HistorianModule")
 * )
 */
