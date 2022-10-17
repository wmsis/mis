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
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Role\PageRequest;
use App\Http\Requests\Role\DeleteRequest;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\StorePermissionRequest;
use App\Http\Requests\Role\StoreApiRequest;
use Illuminate\Database\QueryException;
use App\Models\SIS\API;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     tags={"角色roles"},
     *     operationId="roles",
     *     summary="角色列表分页",
     *     description="使用说明：获取角色列表",
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
     *         description="当前分页",
     *         in="query",
     *         name="page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="每页获取数量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="搜索关键词",
     *         in="query",
     *         name="search",
     *         required=false,
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
    public function index(PageRequest $request){
        $page = $request->input('page');
        $limit = $request->input('num');
        $limit = $limit ? $limit : 10;
        $search = $request->input('search');
        $offset = ($page - 1) * $limit;
        $like = '%'.$search.'%';
        $type_array = array('instation', 'group');//显示集团用户和站内角色

        $total = Role::where('name', 'like', $like)
            ->whereIn('type', $type_array)
            ->where('orgnization_id',  $this->orgnization->id)
            ->count();

        $roles = Role::where('name', 'like', $like)
            ->whereIn('type', $type_array)
            ->where('orgnization_id',  $this->orgnization->id)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        if($roles){
            foreach ($roles as $key=>$item) {
                if($item->type == 'instation'){
                    $roles[$key]->type_name = config('standard.role.instation');
                }
                elseif($item->type == 'group'){
                    $roles[$key]->type_name = config('standard.role.group');
                }
                else{
                    $roles[$key]->type_name = '';
                }
            }

            $res = array(
                'data'=>$roles,
                'total'=>$total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/roles/store",
     *     tags={"角色roles"},
     *     operationId="roleStore",
     *     summary="保存集团角色",
     *     description="使用说明：保存集团角色",
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
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="角色名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="角色描述",
     *         in="query",
     *         name="desc",
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
    public function store(StoreRequest $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $desc = $request->input('desc');
        if($id){
            $role = Role::find($id);
            $role->name = $name;
            $role->type = 'instation';//只保存站内角色
            $role->desc = $desc;
            $res = $role->save();
        }
        else{
            $params = request(['name', 'desc']);
            $params['type'] = 'instation';//只保存站内角色
            $params['orgnization_id'] = $this->orgnization->id;//只保存站内角色
            $res = Role::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
        }

        if($res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}/permission",
     *     tags={"角色roles"},
     *     operationId="rolePermission",
     *     summary="角色权限",
     *     description="使用说明：获取角色权限",
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
     *         description="角色ID",
     *         in="path",
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
    public function permission(Role $role){
        $obj = new Permission();
        $rows = $obj->roots();
        if($rows){
            $permissions = [];
            foreach ($rows as $key => $item) {
                $permissions[] = array(
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
                    'children' => $this->permissionChildren($item->id)
                );
            }
        }

        $myPermissions = $role->permissions; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('permissions', 'myPermissions', 'role');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    private function permissionChildren($parent_id){
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
                'children' => $this->permissionChildren($item->id)
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/roles/{id}/permission",
     *     tags={"角色roles"},
     *     operationId="storeRolePermission",
     *     summary="保存角色权限",
     *     description="使用说明：保存角色权限",
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
     *         description="角色ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="权限ID列表",
     *         in="query",
     *         name="permissions",
     *         required=false,
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
    public function storePermission(Request $request, Role $role){
        //验证
        if($role->type != 'instation'){
            return UtilService::format_data(self::AJAX_FAIL, '集团角色不能编辑', '');
        }

        //获取权限参数
        $param_arr = explode(',', request('permissions'));
        $permissions = Permission::whereIn('id', $param_arr)->get();
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
     * @OA\Post(
     *     path="/api/roles/delete",
     *     tags={"角色roles"},
     *     operationId="roleDelete",
     *     summary="删除角色",
     *     description="使用说明：删除角色",
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
     *         description="角色ID",
     *         in="query",
     *         name="id",
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
    public function delete(DeleteRequest $request){
        $id = $request->input('id');
        $role = Role::find($id);
        $res = $role->delete();
        if($role && $res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles/lists",
     *     tags={"角色roles"},
     *     operationId="roleLists",
     *     summary="角色列表(不分页)",
     *     description="使用说明：获取角色列表",
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
    public function lists(){
        $lists = Role::whereNull('deleted_at')->where('type', 'instation')->where('orgnization_id',  $this->orgnization->id)->get();
        if($lists){
            foreach ($lists as $key=>$item) {
                if($item->type == 'instation'){
                    $lists[$key]->type_name = config('standard.role.instation');
                }
                elseif($item->type == 'group'){
                    $lists[$key]->type_name = config('standard.role.group');
                }
                else{
                    $lists[$key]->type_name = '';
                }
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $lists);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}/api",
     *     tags={"角色roles"},
     *     operationId="roleApi",
     *     summary="角色接口权限",
     *     description="使用说明：获取角色接口权限",
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
     *         description="角色ID",
     *         in="path",
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
    public function api(Role $role){
        $obj = new API();
        $rows = $obj->roots();
        if($rows){
            $apis = [];
            foreach ($rows as $key => $item) {
                $apis[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'title' => $item->name,
                    'sort' => $item->sort,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'description' => $item->description,
                    'children' => $this->apiChildren($item->id)
                );
            }
        }

        $myApis = $role->apis; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('apis', 'myApis', 'role');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    private function apiChildren($parent_id){
        $obj = new API();
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
                'children' => $this->apiChildren($item->id)
            );
        }

        return $arr;
    }

    /**
     * @OA\Post(
     *     path="/api/roles/{id}/api",
     *     tags={"角色roles"},
     *     operationId="storeRoleApi",
     *     summary="保存角色权接口限",
     *     description="使用说明：保存角色接口权限",
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
     *         description="角色ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="接口权限ID列表",
     *         in="query",
     *         name="apis",
     *         required=false,
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
    public function storeApi(Request $request, Role $role){
        //验证
        //获取权限参数
        $param_arr = explode(',', request('apis'));
        $apis = API::whereIn('id', $param_arr)->get();
        //当前角色权限
        $myApis = $role->apis;

        DB::beginTransaction();
        try {
            //要增加的角色
            $addApis = $apis->diff($myApis);
            foreach ($addApis as $api){
                $role->grantApi($api);
            }

            //要删除的角色
            $deleteApis = $myApis->diff($apis);
            foreach ($deleteApis as $api){
                $role->deleteApi($api);
            }

            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
        }
        catch(QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }
}
