<?php
/**
* 后台用户控制器
*
* 后台用户相关接口
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use UtilService;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\User\PageRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\StoreRoleRequest;
use App\Http\Requests\User\SingleDeleteRequest;
use App\Http\Requests\User\BatchDeleteRequest;
use App\Http\Requests\User\ChgpwdRequest;
use App\Http\Requests\User\ResetpwdRequest;
use App\Notifications\TaskFlow;
use App\Models\SIS\HistorianTag;
use App\Http\Requests\User\StoreTagRequest;
use Illuminate\Database\QueryException;
use App\Models\SIS\Orgnization;
use App\Http\Requests\User\StoreOrgnizationRequest;
use App\Models\User;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"用户users"},
     *     operationId="users",
     *     summary="用户列表",
     *     description="使用说明：获取用户列表",
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
     *      ),
     *      @OA\Parameter(
     *         description="角色ID",
     *         in="query",
     *         name="roleid",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
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
        $num = $request->input('num');
        $num = $num ? $num : 10;
        $search = $request->input('search');
        $roleid = $request->input('roleid');
        $offset = ($page - 1) * $num;
        $like = '%' . $search . '%';
        $type_array = array('admin', 'group', 'webmaster');

        $total = User::select(['id'])->whereIn('type', $type_array);
        $users = User::select(['*'])->whereIn('type', $type_array);

        if($search){
            $total = $total->where('name', 'like', $like);
            $users = $users->where('name', 'like', $like);
        }

        if($roleid != 'all' && $roleid) {
            $role = Role::find($roleid);
            $members = $role->users;
            $idarray = array();
            foreach ($members as $member) {
                $idarray[] = $member->id;
            }

            $total = $total->whereIn('id', $idarray);
            $users = $users->whereIn('id', $idarray);
        }

        $total = $total->orderBy('id', 'desc')
            ->count();

        $users = $users->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($num)
            ->get();

        if ($users) {
            foreach ($users as $key=>$item) {
                $roles = $item->roles;
                $role_name = '';
                foreach ($roles as $role){
                    if($role_name){
                        $role_name = $role_name . '，'. $role->name;
                    }
                    else {
                        $role_name = $role->name;
                    }
                }
                $users[$key]['role_name'] = $role_name;

                $orgnizations = $item->orgnizations;
                $orgnization_name = '';
                foreach ($orgnizations as $orgnization){
                    if($orgnization_name){
                        $orgnization_name = $orgnization_name . '，'. $orgnization->name;
                    }
                    else {
                        $orgnization_name = $orgnization->name;
                    }
                }
                $users[$key]['orgnization_name'] = $orgnization_name;

                if($item->type == 'admin'){
                    $users[$key]['type_name'] = '超级管理员';
                }
                elseif($item->type == 'group'){
                    $users[$key]['type_name'] = '集团用户';
                }
                elseif($item->type == 'webmaster'){
                    $users[$key]['type_name'] = '电厂管理员';
                }
                elseif($item->type == 'instation'){
                    $users[$key]['type_name'] = '电厂用户';
                }
                else{
                    $users[$key]['type_name'] = '';
                }
            }

            $res = array(
                'data' => $users,
                'total' => $total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/store",
     *     tags={"用户users"},
     *     operationId="storeUser",
     *     summary="保存用户",
     *     description="使用说明：保存用户",
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
     *         description="id",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户名称",
     *         in="query",
     *         name="name",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户描述",
     *         in="query",
     *         name="desc",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="邮箱",
     *         in="query",
     *         name="email",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="手机",
     *         in="query",
     *         name="mobile",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户省市区",
     *         in="query",
     *         name="area",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="详细地址",
     *         in="query",
     *         name="address",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户类型",
     *         in="query",
     *         name="type",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             default="instation",
     *             @OA\Items(
     *                 type="string",
     *                 enum = {"admin", "group", "webmaster", "instation"},
     *             )
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
        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $area = $request->input('area');
        $address = $request->input('address');
        $isopen = $request->input('isopen');
        $type = $request->input('type');

        $obj = new User();
        $row = $obj->isMobileExist($mobile);

        if (($id && $row && $row->id != $id) || (!$id && $row)) {
            return UtilService::format_data(self::AJAX_FAIL, '该手机号码已存在或已软删除', '');
        } else {
            DB::beginTransaction();
            try {
                if ($id) {
                    $user = User::find($id);
                    $user->name = $name;
                    $user->desc = $desc;
                    $user->email = $email;
                    $user->mobile = $mobile;
                    $user->area = $area;
                    $user->address = $address;
                    $user->isopen = $isopen;
                    $user->type = $type;
                    $user->save();
                }
                else {
                    $params = request(['name', 'desc', 'email', 'type', 'mobile', 'area', 'address', 'isopen']);
                    $params['password'] = bcrypt('123456');
                    User::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
                }
                DB::commit();
                return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
            } catch (QueryException $ex) {
                DB::rollback();
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/role",
     *     tags={"用户users"},
     *     operationId="getUserRole",
     *     summary="用户角色",
     *     description="使用说明：获取用户角色",
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
     *         description="用户ID",
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
    public function role(User $user){
        if($user->type == 'type'){
            $roles = Role::all(); // all roles
        }
        else{
            $roles = Role::where('type', '<>', 'admin')->get();
        }

        $myRoles = $user->roles; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('roles', 'myRoles');
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/role",
     *     tags={"用户users"},
     *     operationId="storeUserRole",
     *     summary="保存用户角色",
     *     description="使用说明：保存用户角色",
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
     *         description="用户ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="角色ID列表",
     *         in="query",
     *         name="roles",
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
    public function storeRole(StoreRoleRequest $request, User $user){
        $roles = $user->roles;

        //验证
        $param_arr = explode(',', request('roles'));
        $roles = Role::whereIn('id', $param_arr)->get();
        $myRoles = $user->roles;

        //要增加的角色
        $addRoles = $roles->diff($myRoles);
        foreach ($addRoles as $role) {
            $user->assignRole($role);
        }

        //要删除的角色
        $deleteRoles = $myRoles->diff($roles);
        foreach ($deleteRoles as $role) {
            $user->deleteRole($role);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, []);
    }

    /**
     * @OA\Post(
     *     path="/api/users/delete",
     *     tags={"用户users"},
     *     operationId="deleteUser",
     *     summary="删除用户",
     *     description="使用说明：删除用户",
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
     *         description="用户ID",
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
    public function delete(SingleDeleteRequest $request){
        $id = $request->input('id');

        $user = User::find($id);
        $res = $user->delete();
        if($user && $res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/batchdelete",
     *     tags={"用户users"},
     *     operationId="batchDeleteUser",
     *     summary="删除用户（批量）",
     *     description="使用说明：删除用户",
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
     *         description="用户id列表",
     *         in="query",
     *         name="idstring",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户密码",
     *         in="query",
     *         name="password",
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
    public function batchdelete(BatchDeleteRequest $request){
        $userObj = auth('admin')->user();
        $idstring = $request->input('idstring');
        $password = $request->input('password');

        //$password = urldecode($password); //前端用encodeURIComponent编码
        //$password = UtilService::aesdecrypt($password);

        if (Hash::check($password, $userObj->password)){
            $idarray = explode(',', $idstring);
            $res = User::whereIn('id', $idarray)->delete();;
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '密码错误', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/chgpwd",
     *     tags={"用户users"},
     *     operationId="chgpwd",
     *     summary="修改用户密码",
     *     description="使用说明：修改用户密码",
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
     *         description="用户ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="原始密码",
     *         in="query",
     *         name="oldpwd",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="新密码",
     *         in="query",
     *         name="newpwd",
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
    public function chgpwd(ChgpwdRequest $request){
        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');
        $id = $request->input('id');

        $user = User::find($id);
        $flag = Hash::check($oldpwd, $user->password);
        if($flag) {
            $user->password = bcrypt($newpwd);
            $res = $user->save();
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '原密码错误', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/resetpwd",
     *     tags={"用户users"},
     *     operationId="resetpwd",
     *     summary="重置密码",
     *     description="使用说明：重置密码",
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
     *         description="用户ID列表",
     *         in="query",
     *         name="idstring",
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
    public function resetpwd(ResetpwdRequest $request){
        $idstring = $request->input('idstring');
        $idarray = explode(',', $idstring);
        $password = bcrypt('123456');
        $res = User::whereIn('id', $idarray)->update([
            'password' => $password
        ]);

        if ($res) {
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}/orgnization",
     *     tags={"用户users"},
     *     operationId="getOrgnization",
     *     summary="用户组织",
     *     description="使用说明：获取用户组织",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="用户ID",
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
    public function orgnization(User $user){
        $orgnizations = Orgnization::all(); // all
        $myOrgnizations = $user->orgnizations; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('orgnizations', 'myOrgnizations');
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/orgnization",
     *     tags={"用户users"},
     *     operationId="storeUserOrgnization",
     *     summary="保存用户组织",
     *     description="使用说明：保存用户组织",
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
     *         description="用户ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="组织ID列表",
     *         in="query",
     *         name="orgnizations",
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
    public function storeOrgnization(StoreOrgnizationRequest $request, User $user){
        //验证
        $param_arr = explode(',', request('orgnizations'));
        $orgnizations = Orgnization::whereIn('id', $param_arr)->get();
        $myOrgnizations = $user->orgnizations;

        DB::beginTransaction();
        try {
            //要增加的组织
            $addOrgnizations = $orgnizations->diff($myOrgnizations);
            foreach ($addOrgnizations as $orgnization) {
                $user->assignOrgnization($orgnization);
            }

            //要删除的组织
            $deleteOrgnizations = $myOrgnizations->diff($orgnizations);
            foreach ($deleteOrgnizations as $orgnization) {
                $user->deleteOrgnization($orgnization);
            }
            DB::commit();
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }
}
