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
     *     description="使用说明：获取用户列表  只获取sass端角色  type=instation",
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
        $type_array = array('instation', 'group', 'webmaster'); //显示集团用户电厂用户及电厂管理员  超级管理员不显示  集团用户不可编辑角色权限

        $obj = DB::table('users')
            ->join('user_orgnization', 'users.id', '=', 'user_orgnization.user_id')
            ->join('orgnization', 'orgnization.id', '=', 'user_orgnization.orgnization_id')
            ->select('users.*')
            ->where('orgnization.ancestor_id',  $this->orgnization->id)
            ->whereNull('users.deleted_at');;

        $total = $obj->whereIn('users.type', $type_array);
        $users = $obj->whereIn('users.type', $type_array);

        if($search){
            $total = $total->where('users.name', 'like', $like);
            $users = $users->where('users.name', 'like', $like);
        }

        if($roleid != 'all' && $roleid) {
            $role = Role::find($roleid);
            $members = $role->users;
            $idarray = array();
            foreach ($members as $member) {
                $idarray[] = $member->id;
            }

            $total = $total->whereIn('users.id', $idarray);
            $users = $users->whereIn('users.id', $idarray);
        }

        $total = $total->distinct()->count();
        $users = $users->distinct()->orderBy('users.id', 'desc')
            ->offset($offset)
            ->limit($num)
            ->get();

        if ($users) {
            foreach ($users as $key=>$item) {
                $user = User::find($item->id);
                //用户角色
                $role_name = '';
                //用户组织
                $orgnization_name = '';
                if($user){
                    $roles = $user->roles;
                    foreach ($roles as $role){
                        if($role_name){
                            $role_name = $role_name . '，'. $role->name;
                        }
                        else {
                            $role_name = $role->name;
                        }
                    }

                    $orgnizations = $user->orgnizations;
                    foreach ($orgnizations as $orgnization){
                        if($orgnization_name){
                            $orgnization_name = $orgnization_name . '，'. $orgnization->name;
                        }
                        else {
                            $orgnization_name = $orgnization->name;
                        }
                    }
                }
                $users[$key]->role_name = $role_name;
                $users[$key]->orgnization_name = $orgnization_name;

                if($item->type == 'instation'){
                    $users[$key]->type_name = config('standard.user.instation');
                }
                elseif($item->type == 'webmaster'){
                    $users[$key]->type_name = config('standard.user.webmaster');
                }
                elseif($item->type == 'group'){
                    $users[$key]->type_name = config('standard.user.group');
                }
                else{
                    $users[$key]->type_name = '';
                }
            }

            $res = array(
                'data' => $users,
                'total' => $total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
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
     *         required=false,
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
     *                 enum = {"instation"},
     *             )
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="岗位ID",
     *         in="query",
     *         name="job_station_id",
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
    public function store(StoreRequest $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $desc = $request->input('desc');
        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $area = $request->input('area');
        $address = $request->input('address');
        $isopen = $request->input('isopen');
        $job_station_id = $request->input('job_station_id');

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
                    $user->job_station_id = $job_station_id;
                    $user->save();
                }
                else {
                    $params = request(['name', 'desc', 'email', 'type', 'mobile', 'area', 'address', 'isopen', 'job_station_id']);
                    $params['password'] = bcrypt('123456');
                    $params['type'] = 'instation'; //只能创建组织内部用户
                    $user = User::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
                    $user->orgnizations()->save($this->orgnization); //保存当前二级组织（电厂）
                }
                DB::commit();
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
            } catch (QueryException $ex) {
                DB::rollback();
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/role",
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
        if($user->type == 'admin'){
            $roles = Role::where('type', 'group')
                ->orWhere(function($query) {
                    $query->where('type', 'instation')
                          ->where('orgnization_id', $this->orgnization->id);
                })
                ->get(); // 管理员显示集团角色和站内角色
        }
        else{
            //非管理员只显示该组织的站内角色
            $roles = Role::where('orgnization_id', $this->orgnization->id)->where('type', 'instation')->get();
        }

        $myRoles = $user->roles; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('roles', 'myRoles');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{user}/role",
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
        if($user->type != 'instation'){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_GROUP_ROLE_MSG, '');
        }
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
        $org = $user->orgnizations()->where('orgnization_id', $this->orgnization->id)->first();
        if(!$org || !$org->id){
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_ILLEGAL_MSG, '');
        }
        $res = $user->delete();
        if($user && $res){
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
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
        $userObj = auth('api')->user();
        $idstring = $request->input('idstring');
        $password = $request->input('password');

        //$password = urldecode($password); //前端用encodeURIComponent编码
        //$password = UtilService::aesdecrypt($password);

        if (Hash::check($password, $userObj->password)){
            $idarray = explode(',', $idstring);
            $res = User::whereIn('id', $idarray)->delete();;
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
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

        $user = auth('api')->user();
        $flag = Hash::check($oldpwd, $user->password);
        if($flag) {
            $user->password = bcrypt($newpwd);
            $res = $user->save();
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
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
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users/bind-member",
     *     tags={"用户users"},
     *     operationId="bind-member",
     *     summary="綁定前端用戶(SASS端用户使用)",
     *     description="使用说明：綁定前端用戶",
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
     *         description="member_id",
     *         in="query",
     *         name="member_id",
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
    public function bindMember(Request $request){
        $user = auth('api')->user();
        $member_id = $request->input('member_id');
        $user->member_id = $member_id;
        $res = $user->save();

        if ($res) {
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/orgnization",
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
        $orgnizations = Orgnization::where('ancestor_id', $this->orgnization->id)->get(); // all
        $myOrgnizations = $user->orgnizations; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('orgnizations', 'myOrgnizations');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{user}/orgnization",
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
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        } catch (QueryException $ex) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }
}
