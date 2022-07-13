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
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\PageRequest;
use App\Http\Requests\Admin\StoreRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\SingleDeleteRequest;
use App\Http\Requests\Admin\BatchDeleteRequest;
use App\Http\Requests\Admin\ChgpwdRequest;
use App\Http\Requests\Admin\ResetpwdRequest;
use App\Notifications\TaskFlow;
use App\Http\Models\SIS\HistorianTag;
use App\Http\Requests\Admin\StoreTagRequest;

class AdminController extends Controller
{

    /**
     * @SWG\Get(
     *     path="/api/admins",
     *     tags={"admins api"},
     *     operationId="admins",
     *     summary="用户列表",
     *     description="使用说明：获取用户列表",
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
     *      @SWG\Parameter(
     *         description="角色ID",
     *         in="query",
     *         name="roleid",
     *         required=true,
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
        $num = $request->input('num');
        $num = $num ? $num : 10;
        $search = $request->input('search');
        $roleid = $request->input('roleid');
        $offset = ($page - 1) * $num;
        $like = '%' . $search . '%';

        $total = \App\User::select(['id']);
        $users = \App\User::select(['*']);

        if($search){
            $total = $total->where('name', 'like', $like);
            $users = $users->where('name', 'like', $like);
        }

        if($roleid != 'all') {
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
     * @SWG\Post(
     *     path="/api/admins/store",
     *     tags={"admins api"},
     *     operationId="storeUser",
     *     summary="保存用户",
     *     description="使用说明：保存用户",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户id",
     *         in="query",
     *         name="id",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="用户名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户描述",
     *         in="query",
     *         name="desc",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="邮箱",
     *         in="query",
     *         name="email",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="手机",
     *         in="query",
     *         name="mobile",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户省市区",
     *         in="query",
     *         name="area",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="详细地址",
     *         in="query",
     *         name="address",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="isopen",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="用户类型",
     *         in="query",
     *         name="type",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="班次",
     *         in="query",
     *         name="period",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function store(StoreRequest $request){
        $userObj = auth()->user();
        $roles = $userObj->roles;

        $id = $request->input('id');
        $name = $request->input('name');
        $desc = $request->input('desc');
        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $area = $request->input('area');
        $address = $request->input('address');
        $isopen = $request->input('isopen');
        $type = $request->input('type');
        $period = $request->input('period');

        $obj = new \App\User();
        $row = $obj->isMobileExist($mobile);

        if (($id && $row && $row->id != $id) || (!$id && $row)) {
            return UtilService::format_data(self::AJAX_FAIL, '该手机号码已存在或已软删除', '');
        } else {
            DB::beginTransaction();
            try {
                if ($id) {
                    $user = \App\User::find($id);
                    $user->name = $name;
                    $user->desc = $desc;
                    $user->email = $email;
                    $user->mobile = $mobile;
                    $user->area = $area;
                    $user->address = $address;
                    $user->isopen = $isopen;
                    $user->type = $type;
                    $user->period = $period;
                    $user->save();
                }
                else {
                    $params = request(['name', 'desc', 'email', 'type', 'mobile', 'area', 'address', 'isopen', 'period']);
                    $params['password'] = bcrypt('123456');
                    \App\User::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
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
     * @SWG\Get(
     *     path="/api/admins/{id}/role",
     *     tags={"admins api"},
     *     operationId="getRole",
     *     summary="用户角色",
     *     description="使用说明：获取用户角色",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户ID",
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
    public function role(\App\User $user){
        $roles = \App\Role::all(); // all roles
        $myRoles = $user->roles; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('roles', 'myRoles');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @SWG\Post(
     *     path="/api/admins/{id}/role",
     *     tags={"admins api"},
     *     operationId="storeRole",
     *     summary="保存用户角色",
     *     description="使用说明：保存用户角色",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="角色ID列表",
     *         in="query",
     *         name="roles",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storeRole(StoreRoleRequest $request, \App\User $user){
        $userObj = auth()->user();
        $roles = $userObj->roles;

        //验证
        $roles = \App\Role::findMany(request('roles'));
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

        return UtilService::format_data(self::AJAX_SUCCESS, '保存成功', []);
    }

    /**
     * @SWG\Post(
     *     path="/api/admins/delete",
     *     tags={"admins api"},
     *     operationId="deleteUser",
     *     summary="删除用户",
     *     description="使用说明：删除用户",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户ID",
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
    public function delete(SingleDeleteRequest $request){
        $id = $request->input('id');

        $user = \App\User::find($id);
        $res = $user->delete();
        if($user && $res){
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/admins/batchdelete",
     *     tags={"admins api"},
     *     operationId="batchDeleteUser",
     *     summary="删除用户（批量）",
     *     description="使用说明：删除用户",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户id列表",
     *         in="query",
     *         name="idstring",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户密码",
     *         in="query",
     *         name="password",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function batchdelete(BatchDeleteRequest $request){
        $userObj = auth()->user();
        $idstring = $request->input('idstring');
        $password = $request->input('password');

        //$password = urldecode($password); //前端用encodeURIComponent编码
        //$password = UtilService::aesdecrypt($password);

        if (Hash::check($password, $userObj->password)){
            $idarray = explode(',', $idstring);
            $res = \App\User::whereIn('id', $idarray)->delete();;
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
     * @SWG\Post(
     *     path="/api/admins/chgpwd",
     *     tags={"admins api"},
     *     operationId="chgpwd",
     *     summary="修改用户密码",
     *     description="使用说明：修改用户密码",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="原始密码",
     *         in="query",
     *         name="oldpwd",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="新密码",
     *         in="query",
     *         name="newpwd",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function chgpwd(ChgpwdRequest $request){
        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');

        $user = auth()->user();
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
     * @SWG\Post(
     *     path="/api/admins/resetpwd",
     *     tags={"admins api"},
     *     operationId="resetpwd",
     *     summary="重置密码",
     *     description="使用说明：重置密码",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户ID列表",
     *         in="query",
     *         name="idstring",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function resetpwd(ResetpwdRequest $request){
        $user = auth()->user();
        $roles = $user->roles;

        $idstring = $request->input('idstring');
        $idarray = explode(',', $idstring);
        $password = bcrypt('123456');
        $res = \App\User::whereIn('id', $idarray)->update([
            'password' => $password
        ]);

        if ($res) {
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/admins/{id}/tag",
     *     tags={"admins api"},
     *     operationId="getUserTag",
     *     summary="用户TAG",
     *     description="使用说明：获取用户TAG",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户ID",
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
    public function tag(\App\User $user){
        $tags = HistorianTag::select(['id', 'tag_name', 'description', 'alias'])->get(); // all tags
        $myTags = $user->tags; //带括号的是返回关联对象实例，不带括号是返回动态属性

        //compact 创建一个包含变量名和它们的值的数组
        $data = compact('tags', 'myTags');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @SWG\Post(
     *     path="/api/admins/{id}/tag",
     *     tags={"admins api"},
     *     operationId="storeUserTag",
     *     summary="保存用户TAG",
     *     description="使用说明：保存用户TAG",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="TAG ID列表",
     *         in="query",
     *         name="tags",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storeTag(StoreTagRequest $request, \App\User $user){
        //验证
        $tags = HistorianTag::findMany(request('tags'));
        $myTags = $user->tags;

        //要增加的角色
        $addTags = $tags->diff($myTags);
        foreach ($addTags as $tag) {
            $user->assignTag($tag);
        }

        //要删除的角色
        $deleteTags = $myTags->diff($tags);
        foreach ($deleteTags as $tag) {
            $user->deleteTag($tag);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, '保存成功', []);
    }

    /**
     * @SWG\Post(
     *     path="/api/admins/bind-member",
     *     tags={"admins api"},
     *     operationId="bind-member",
     *     summary="綁定前端用戶",
     *     description="使用说明：綁定前端用戶",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="member_id",
     *         in="formData",
     *         name="member_id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function bindMember(Request $request){
        $user = auth()->user();
        $member_id = $request->input('member_id');
        $user->member_id = $member_id;
        $res = $user->save();

        if ($res) {
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }
}
