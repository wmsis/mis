<?php

/**
* tag分组控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Models\SIS\TagGroup;
use App\Http\Requests\API\StoreTagGroupRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use UtilService;

class TagGroupController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/tag-group/index",
     *     tags={"tag group api"},
     *     operationId="",
     *     summary="获取 tag group 列表",
     *     description="使用说明：获取 tag group 列表",
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
     *              property="TagGroups",
     *              description="TagGroups",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/TagGroups")
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

        $groups = TagGroup::select(['*']);
        if ($name) {
            $groups = $groups->where('name', 'like', "%{$name}%");
        }
        $total = $groups->count();
        $groups = $groups->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $groups, 'total' => $total]));

    }

    /**
     * @SWG\GET(
     *     path="/api/tag-group/show/{id}",
     *     tags={"tag group api"},
     *     operationId="",
     *     summary="获取 tag group 详细信息",
     *     description="使用说明：获取 tag group 详细信息",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag group 主键",
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
     *              property="TagGroup",
     *              description="TagGroup",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/TagGroup")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function show($id)
    {
        $group = TagGroup::find($id);
        if (!$group) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $group));
    }

    /**
     * @SWG\POST(
     *     path="/api/tag-group/store",
     *     tags={"tag group api"},
     *     operationId="",
     *     summary="新增 tag group",
     *     description="使用说明：新增 tag group",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag group name",
     *     in="formData",
     *     name="name",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="tag group description",
     *     in="formData",
     *     name="description",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="store succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="TagGroup",
     *              description="TagGroup",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/TagGroup")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function store(StoreTagGroupRequest $request)
    {
        $input = $request->only(['name', 'description']);
        try {
            $res = TagGroup::create($input);
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '操作失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res));
    }

    /**
     * @SWG\POST(
     *     path="/api/tag-group/update/{id}",
     *     tags={"tag group api"},
     *     operationId="",
     *     summary="修改 tag group",
     *     description="使用说明：修改 tag group",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag group 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="tag group name",
     *     in="formData",
     *     name="name",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="tag group description",
     *     in="formData",
     *     name="description",
     *     required=false,
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
        $group = TagGroup::find($id);
        if (!$group) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Module不存在', ''));
        }
        if ($name = $request->input('name')) {
            $group->name = $name;
        }
        if ($description = $request->input('description')) {
            $group->description = $description;
        }
        try {
            $group->save();
        } catch (QueryException $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', ''));
    }

    /**
     * @SWG\DELETE(
     *     path="/api/tag-group/destroy/{id}",
     *     tags={"tag group api"},
     *     operationId="",
     *     summary="删除 tag group",
     *     description="使用说明：删除 tag group",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag group 主键",
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
        $group = TagGroup::find($id);
        if (!$group) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        try {
            $group->delete();
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }
}

/**
 * @SWG\Definition(
 *     definition="TagGroups",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/TagGroup")
 * )
 */
