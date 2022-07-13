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
use App\Permission;
use App\Http\Requests\Permission\ChildrenRequest;
use App\Http\Requests\Permission\DeleteRequest;
use App\Http\Requests\Permission\InsertRequest;
use App\Http\Requests\Permission\UpdateRequest;

class PermissionController extends Controller
{

    /**
     * @SWG\Get(
     *     path="/api/permissions",
     *     tags={"permissions api"},
     *     operationId="permissions",
     *     summary="权限列表",
     *     description="使用说明：获取权限列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function index(){
        $obj = new Permission();
        $permissions = $obj->lists();
        if($permissions){
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $permissions);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/permissions/all",
     *     tags={"permissions api"},
     *     operationId="permissionsAll",
     *     summary="权限列表(所有)",
     *     description="使用说明：获取权限列表(所有)",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function all(){
        $permissions = Permission::orderBy('sort', 'ASC')->get();
        if($permissions){
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $permissions);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/permissions/insert",
     *     tags={"permissions api"},
     *     operationId="permissionsInsert",
     *     summary="插入权限节点",
     *     description="使用说明：获取插入权限节点",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限节点名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限父节点path",
     *         in="query",
     *         name="parent_path",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="颜色",
     *         in="query",
     *         name="color",
     *         required=false,
     *         type="string",
     *     ),
     *      @SWG\Parameter(
     *         description="页面URL",
     *         in="query",
     *         name="page_url",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="接口名",
     *         in="query",
     *         name="api_name",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="接口URL",
     *         in="query",
     *         name="api_url",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="是否根节点",
     *         in="query",
     *         name="is_root",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="权限类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function insert(InsertRequest $request){
        $obj = new Permission();

        $name = $request->input('name');
        $parent_path = $request->input('parent_path');
        $sort = $request->input('sort');
        $color = $request->input('color');
        $is_root = $request->input('is_root');
        $type = $request->input('type');
        $api_name = $request->input('api_name');
        $api_url = $request->input('api_url');
        $page_url = $request->input('page_url');
        $icon = $request->input('icon');

        if($is_root){
            $level = 1;
        }
        elseif($parent_path && strpos($parent_path, '/') !== false){
            $pathArray = explode('/', $parent_path);
            $level = count($pathArray) + 1;
        }
        elseif($parent_path && strpos($parent_path, '/') === false){
            $level = 2;
        }

        $p = array(
            'name' => $name,
            'level' => $level,
            'is_root' => $is_root,
            'sort' => $sort,
            'icon' => $icon,
            'color' => $color,
            'type' => $type,
            'page_url' => $page_url,
            'created_at' => date('Y-m-d H:i:s', time())
        );

        if($type == 'look' || $type == 'button'){
            $p['api_url'] = $api_url;
            $p['api_name'] = $api_name;
        }

        DB::beginTransaction();
        try {
            $id = $obj->insert($p);
            $params = [];
            $params['updated_at'] = date('Y-m-d H:i:s', time());
            if($is_root){
                $path = $id;
            }
            else{
                $path = $parent_path."/".$id;
            }
            $row = $obj->rowById($id);
            $row->path = $path;
            $row->save();

            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/permissions/update",
     *     tags={"permissions api"},
     *     operationId="permissionsUpdate",
     *     summary="修改权限分类",
     *     description="使用说明：获取修改权限分类",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限节点名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="颜色",
     *         in="query",
     *         name="color",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="节点ID",
     *         in="query",
     *         name="id",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="权限排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         type="integer",
     *     ),
     *      @SWG\Parameter(
     *         description="权限名称",
     *         in="query",
     *         name="api_name",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限URL",
     *         in="query",
     *         name="api_url",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="页面URL",
     *         in="query",
     *         name="page_url",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
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
        $api_url = $request->input('api_url');
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
            if($type == 'look' || $type == 'button'){
                $row->api_name = $api_name;
                $row->api_url = $api_url;
            }
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
     * @SWG\Post(
     *     path="/api/permissions/delete",
     *     tags={"permissions api"},
     *     operationId="permissionsDelete",
     *     summary="删除权限分类",
     *     description="使用说明：删除权限及其子权限",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function delete(DeleteRequest $request){
        $obj = new Permission();
        $id = $request->input('id');
        $row = $obj->rowById($id);
        if($row){
            $res = $row->delete();
            $like = $row->path.'/%';
            Permission::where('path', 'like', $like)->delete();
            if($res){
                return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', ['id'=>$id]);
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据出错', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/permissions/children",
     *     tags={"permissions api"},
     *     operationId="permissionsChildren",
     *     summary="权限分类子节点",
     *     description="使用说明：权限分类子节点",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限path",
     *         in="query",
     *         name="path",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function children(ChildrenRequest $request){
        $path = $request->input('path');
        $obj = new Permission();
        $lists = $obj->children($path);
        if($lists){
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
        }
        else{
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', []);
        }
    }
}
