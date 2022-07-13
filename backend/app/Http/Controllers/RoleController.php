<?php
/**
* 角色控制器
*
* 角色相关接口
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use UtilService;
use App\Role;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Role\PageRequest;
use App\Http\Requests\Role\DeleteRequest;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\StorePermissionRequest;

class RoleController extends Controller
{

    /**
     * @SWG\Get(
     *     path="/api/roles",
     *     tags={"roles api"},
     *     operationId="roles",
     *     summary="角色列表分页",
     *     description="使用说明：获取角色列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="当前分页",
     *         in="query",
     *         name="page",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="每页获取数量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="搜索关键词",
     *         in="query",
     *         name="search",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function index(PageRequest $request){
        $page = $request->input('page');
        $limit = $request->input('num');
        $limit = $limit ? $limit : 10;
        $search = $request->input('search');
        $offset = ($page - 1) * $limit;
        $like = '%'.$search.'%';

        $total = \App\Role::where('name', 'like', $like)
            ->orderBy('id', 'desc')
            ->get();

        $roles = \App\Role::where('name', 'like', $like)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        if($roles){
            $res = array(
                'data'=>$roles,
                'total'=>count($total)
            );
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/roles/store",
     *     tags={"roles api"},
     *     operationId="roleStore",
     *     summary="保存角色",
     *     description="使用说明：保存角色",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="id（修改时需要）",
     *         in="query",
     *         name="id",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="角色名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="角色描述",
     *         in="query",
     *         name="desc",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function store(StoreRequest $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $desc = $request->input('desc');
        if($id){
            $role = \App\Role::find($id);
            $role->name = $name;
            $role->desc = $desc;
            $res = $role->save();
        }
        else{
            $res = \App\Role::create(request(['name', 'desc'])); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
        }

        if($res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/roles/{id}/permission",
     *     tags={"roles api"},
     *     operationId="rolePermission",
     *     summary="角色权限",
     *     description="使用说明：获取角色权限",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="角色ID",
     *         in="path",
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
    public function permission(\App\Role $role){
        $permissions = \App\Permission::all(); // all permissions
        $myPermissions = $role->permissions; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('permissions', 'myPermissions', 'role');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @SWG\Post(
     *     path="/api/roles/{id}/permission",
     *     tags={"roles api"},
     *     operationId="storeRolePermission",
     *     summary="保存角色权限",
     *     description="使用说明：保存角色权限",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="角色ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="权限ID列表",
     *         in="query",
     *         name="permissions",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storePermission(StorePermissionRequest $request, \App\Role $role){
        //验证
        //获取权限参数
        $permissions = \App\Permission::findMany(request('permissions'));
        //当前角色权限
        $myPermissions = $role->permissions;

        DB::beginTransaction();
        try {
            //要增加的角色
            $addPermissions = $permissions->diff($myPermissions);
            foreach ($addPermissions as $permission){
                $role->grantPermission($permission);
            }

            //要删除的角色
            $deletePermissions = $myPermissions->diff($permissions);
            foreach ($deletePermissions as $permission){
                $role->deletePermission($permission);
            }

            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
        }
        catch(QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/roles/delete",
     *     tags={"roles api"},
     *     operationId="roleDelete",
     *     summary="删除角色",
     *     description="使用说明：删除角色",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="角色ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function delete(DeleteRequest $request){
        $id = $request->input('id');
        $role = \App\Role::find($id);
        $res = $role->delete();
        if($role && $res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/roles/lists",
     *     tags={"roles api"},
     *     operationId="roleLists",
     *     summary="角色列表(不分页)",
     *     description="使用说明：获取角色列表",
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
    public function lists(){
        $lists = Role::whereNull('deleted_at')->get();
        if($lists){
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }
}
