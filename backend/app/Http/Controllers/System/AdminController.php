<?php
/**
* 总管理员控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use MyCacheService;
use App\Models\System\Admin;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Hash;
use Log;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login', 'refresh']]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"系统管理员admin"},
     *     operationId="admin login",
     *     summary="登录",
     *     description="使用说明：登录",
     *     @OA\Parameter(
     *         description="用户名",
     *         in="query",
     *         name="username",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="密码",
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
    public function login()
    {
        $credentials = request(['username', 'password']);
        $key = UtilService::getKey($credentials['username'], 'SYSTEM_TOKEN');
        $admin = Admin::where('username', $credentials['username'])->first();
        if($admin){
            $current_token = MyCacheService::getCache($key);
            if($current_token){
                //将老token加入黑名单
                JWTAuth::setToken($current_token)->invalidate();
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '用户不存在', '');
        }

        if (! $token = auth('admin')->attempt($credentials)) {
            return UtilService::format_data(self::AJAX_FAIL, '用户名或者密码错误', '');
        }
        $expire = auth('admin')->factory()->getTTL() * 60;
        MyCacheService::setCache($key, $token, $expire);

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/me",
     *     tags={"系统管理员admin"},
     *     operationId="admin me",
     *     summary="我",
     *     description="使用说明：我",
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
    public function me()
    {
        $user = auth('admin')->user();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $user);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/logout",
     *     tags={"系统管理员admin"},
     *     operationId="admin logout",
     *     summary="退出登录",
     *     description="使用说明：退出登录",
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
    public function logout()
    {
        $admin = auth('admin')->user();
        if($admin && isset($admin->username)) {
            $key = UtilService::getKey($admin->username, 'SYSTEM_TOKEN');
            MyCacheService::clearCache($key);
        }

        auth('admin')->logout();
        return UtilService::format_data(self::AJAX_SUCCESS, '退出成功', '');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/refresh",
     *     tags={"系统管理员admin"},
     *     operationId="admin refresh",
     *     summary="刷新token",
     *     description="使用说明：刷新token",
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
    public function refresh()
    {
        $admin = auth('admin')->user();
        $token = auth('admin')->refresh();
        if($admin) {
            $key = UtilService::getKey($admin->username, 'SYSTEM_TOKEN');
            MyCacheService::setCache($key, $token, 3600);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @param $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        $rtn = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60
        ];
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $rtn);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/chgpwd",
     *     tags={"系统管理员admin"},
     *     operationId="admin chgpwd",
     *     summary="修改管理员密码",
     *     description="使用说明：修改管理员密码",
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
    public function chgpwd(Request $request){
        $oldpwd = $request->input('oldpwd');
        $newpwd = $request->input('newpwd');

        $user = auth('admin')->user();
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
     *     path="/api/admin/resetpwd",
     *     tags={"系统管理员admin"},
     *     operationId="admin resetpwd",
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
    public function resetpwd(Request $request){
        $idstring = $request->input('idstring');
        $idarray = explode(',', $idstring);
        $password = bcrypt('123456');
        $res = Admin::whereIn('id', $idarray)->update([
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
     *     path="/api/admin/store",
     *     tags={"系统管理员admin"},
     *     operationId="storeAdmin",
     *     summary="保存管理员",
     *     description="使用说明：保存管理员",
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
     *         description="管理员名称",
     *         in="query",
     *         name="username",
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="管理员昵称",
     *         in="query",
     *         name="nickname",
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
    public function store(Request $request){
        $id = $request->input('id');
        $username = $request->input('username');
        $nickname = $request->input('nickname');
        $password = $request->input('password');

        $row = Admin::where('username', $username)->first();
        if (($id && $row && $row->id != $id) || (!$id && $row)) {
            return UtilService::format_data(self::AJAX_FAIL, '该用户已存在或已软删除', '');
        } else {
            DB::beginTransaction();
            try {
                if ($id) {
                    $admin = Admin::find($id);
                    $admin->username = $username;
                    $admin->nickname = $nickname;
                    $admin->save();
                }
                else {
                    $params = request(['username', 'nickname']);
                    $params['type'] = 'system';
                    $params['password'] = bcrypt($password);
                    Admin::create($params); //save 和 create 的不同之处在于 save 接收整个 Eloquent 模型实例而 create 接收原生 PHP 数组
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
     * @OA\Post(
     *     path="/api/admin/delete",
     *     tags={"系统管理员admin"},
     *     operationId="deleteAdmin",
     *     summary="删除管理员",
     *     description="使用说明：删除管理员",
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
     *         description="管理员ID",
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
    public function delete(Request $request){
        $id = $request->input('id');
        $user = Admin::find($id);
        if($user){
            $user->delete();
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/page",
     *     tags={"系统管理员admin"},
     *     operationId="admins",
     *     summary="管理员列表",
     *     description="使用说明：管理员列表",
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
     *         name="username",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function page(Request $request){
        $page = $request->input('page');
        $num = $request->input('num');
        $num = $num ? $num : 10;
        $username = $request->input('username');
        $offset = ($page - 1) * $num;
        $like = '%' . $username . '%';

        $total = Admin::select(['id']);
        $admins = Admin::select(['*']);

        if($username){
            $total = $total->where('username', 'like', $like);
            $admins = $admins->where('username', 'like', $like);
        }

        $total = $total->count();
        $admins = $admins->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($num)
            ->get();

        foreach ($admins as $key=>$item) {
            if($item->type == 'system'){
                $admins[$key]['type_name'] = '系统超级管理员';
            }
            elseif($item->type == 'guest'){
                $admins[$key]['type_name'] = '游客';
            }
            else{
                $admins[$key]['type_name'] = '';
            }
        }

        $res = array(
            'data' => $admins,
            'total' => $total
        );
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }
}
