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
use CacheService;
use App\Models\System\Admin;
use JWTAuth;
use Hash;

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
            $current_token = CacheService::getCache($key);
            if($current_token){
                //将老token加入黑名单
                JWTAuth::unsetToken();
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '用户不存在', '');
        }

        if (! $token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => '用户名或者密码错误'], 401);
        }
        $expire = auth('admin')->factory()->getTTL() * 60;
        CacheService::setCache($key, $token, $expire);

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
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $user);
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
            CacheService::clearCache($key);
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
            CacheService::setCache($key, $token, 3600);
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
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $rtn);
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
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
    }
}
