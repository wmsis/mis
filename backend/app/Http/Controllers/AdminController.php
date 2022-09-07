<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use UtilService;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login']]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"admin api"},
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
        if (! $token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => '用户名或者密码错误'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/me",
     *     tags={"admin api"},
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
        return UtilService::format_data(self::AJAX_SUCCESS, '退出成功', $user);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/logout",
     *     tags={"admin api"},
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
        auth('admin')->logout();
        return UtilService::format_data(self::AJAX_SUCCESS, '退出成功', '');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/refresh",
     *     tags={"admin api"},
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
        return $this->respondWithToken(auth('admin')->refresh());
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
}
