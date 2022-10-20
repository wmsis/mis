<?php
/**
* 权限控制器
*
* 权限相关接口
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use UtilService;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Http\Requests\Permission\DeleteRequest;
use App\Http\Requests\Permission\InsertRequest;
use App\Http\Requests\Permission\UpdateRequest;

class PermissionController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/permissions/tree",
     *     tags={"菜单权限permissions"},
     *     operationId="permissionsAll",
     *     summary="菜单权限列表(所有)",
     *     description="使用说明：获取菜单权限列表(所有)",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function tree(){
        $obj = new Permission();
        $rows = $obj->roots();
        if($rows){
            $arr = [];
            foreach ($rows as $key => $item) {
                $arr[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'description' => $item->description,
                    'color' => $item->color,
                    'page_url' => $item->page_url,
                    'api_name' => $item->api_name,
                    'type' => $item->type,
                    'icon' => $item->icon,
                    'children' => $this->children($item->id)
                );
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $arr);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', []);
        }
    }

    private function children($parent_id){
        $obj = new Permission();
        $rows = $obj->children($parent_id);
        $arr = [];
        foreach ($rows as $key => $item) {
            $arr[] = array(
                'id' => $item->id,
                'name' => $item->name,
                'title' => $item->name,
                'sort' => $item->sort,
                'parent_id' => $item->parent_id,
                'level' => $item->level,
                'description' => $item->description,
                'color' => $item->color,
                'page_url' => $item->page_url,
                'api_name' => $item->api_name,
                'type' => $item->type,
                'icon' => $item->icon,
                'children' => $this->children($item->id)
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/permissions/insert",
     *     tags={"菜单权限permissions"},
     *     operationId="permissionsInsert",
     *     summary="插入权限节点",
     *     description="使用说明：获取插入权限节点",
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
     *         description="权限节点名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="权限父节点ID",
     *         in="query",
     *         name="parent_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="权限排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="颜色",
     *         in="query",
     *         name="color",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *      @OA\Parameter(
     *         description="页面URL",
     *         in="query",
     *         name="page_url",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="接口名",
     *         in="query",
     *         name="api_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="权限类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function insert(InsertRequest $request){
        $obj = new Permission();

        $name = $request->input('name');
        $parent_id = $request->input('parent_id');
        $sort = $request->input('sort');
        $color = $request->input('color');
        $type = $request->input('type');
        $api_name = $request->input('api_name');
        $level = 1;
        $page_url = $request->input('page_url');
        $icon = $request->input('icon');

        if($parent_id){
            $parent = Permission::find($parent_id);
            $level = $parent && $parent->level ? $parent->level + 1 : 1;
        }

        $p = array(
            'name' => $name,
            'level' => $level,
            'sort' => $sort,
            'icon' => $icon,
            'color' => $color,
            'type' => $type,
            'page_url' => $page_url,
            'api_name' => $api_name,
            'parent_id' => $parent_id,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time())
        );

        DB::beginTransaction();
        try {
            $id = $obj->insert($p);
            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/permissions/update",
     *     tags={"菜单权限permissions"},
     *     operationId="permissionsUpdate",
     *     summary="修改权限分类",
     *     description="使用说明：获取修改权限分类",
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
     *         description="权限节点名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="颜色",
     *         in="query",
     *         name="color",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="节点ID",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="权限排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *      @OA\Parameter(
     *         description="权限名称",
     *         in="query",
     *         name="api_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="页面URL",
     *         in="query",
     *         name="page_url",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="权限类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function update(UpdateRequest $request){
        $obj = new Permission();
        $name = $request->input('name');
        $sort = $request->input('sort');
        $id = $request->input('id');
        $api_name = $request->input('api_name');
        $page_url = $request->input('page_url');
        $type = $request->input('type');
        $icon = $request->input('icon');
        $color = $request->input('color');

        $row = $obj->rowById($id);
        if($row){
            $row->name = $name;
            $row->sort = $sort;
            $row->page_url = $page_url;
            $row->type = $type;
            $row->icon = $icon;
            $row->color = $color;
            $row->api_name = $api_name;
            $res = $row->save();
            if($res){
                return UtilService::format_data(self::AJAX_SUCCESS, '修改成功', '');
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, '修改失败', '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据出错', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/permissions/delete",
     *     tags={"菜单权限permissions"},
     *     operationId="permissionsDelete",
     *     summary="删除权限分类",
     *     description="使用说明：删除权限及其子权限",
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
     *         description="ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function delete(DeleteRequest $request){
        DB::beginTransaction();
        try {
            $id = $request->input('id');
            $this->loopdel($id);
            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
    }

    private function loopdel($id){
        $obj = new Permission();
        $row = $obj->rowById($id);
        if($row){
            $res = $row->delete();
            if($res){
                $rows = Permission::where('parent_id', $id)->get()->toArray();
                if($rows && count($rows) > 0){
                    foreach ($rows as $key => $item) {
                        $this->loopdel($item['id']);
                    }
                    Permission::where('parent_id', $id)->delete();
                }
            }
        }
    }
}
