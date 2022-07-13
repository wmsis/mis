<?php
/**
* 认证控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Events\UserLoginEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\UserLoginRequest;
use App\Http\Requests\API\UserRegistRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Hash;
use App\User;
use UtilService;
use CacheService;
use App\Http\Models\OperateLog;
use App\Permission;

/**
 * @SWG\Swagger(
 *     swagger="2.0",
 *     schemes={"http"},
 *     host="wmmis-data.com/",
 *     basePath="",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="SIS API 文档",
 *         description="伟明环保设备有限公司SIS系统 API 文档 api/documentation  php artisan l5-swagger:generate"
 *     )
 * )
 */
class AuthController extends Controller
{

    private function getKey($mobile){
        return md5($mobile . '_TOKEN');
    }

    /**
     * @SWG\Post(
     *     path="/api/auth/login",
     *     tags={"auth api"},
     *     operationId="",
     *     summary="登录",
     *     description="使用说明：登录",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="手机号码",
     *         in="formData",
     *         name="mobile",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="密码",
     *         in="formData",
     *         name="password",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="privileges",
     *                   description="privileges",
     *                   allOf={
     *                        @SWG\Schema(ref="#/definitions/Privileges")
     *                   }
     *              ),
     *             @SWG\Property(
     *                   property="user",
     *                   description="user",
     *                   allOf={
     *                        @SWG\Schema(ref="#/definitions/User")
     *                   }
     *              ),
     *              @SWG\Property(
     *                   property="token",
     *                   type="string",
     *                   description="token"
     *              ),
     *         )
     *     )
     * )
     */
    public function login(UserLoginRequest $request)
    {
        $params = $request->only('mobile', 'password');
        $credentials = array(
            "mobile" => $params['mobile'],
            "password" => $params['password'],
            "isopen" => 1
        );

        $user = User::where('mobile', $credentials['mobile'])->first();
        try {
            $key = $this->getKey($params['mobile']);
            $current_token = CacheService::getCache($key);
            if($current_token){
                //将老token加入黑名单
                JWTAuth::setToken($current_token)->invalidate();
            }

            if(!$user){
                $res = UtilService::format_data(self::AJAX_FAIL, '账号不存在', '');
                return response()->json($res, 401);
            }
            // attempt to verify the credentials and create a token for the user
            elseif (!$token = auth()->attempt($credentials)) {
                $res = UtilService::format_data(self::AJAX_FAIL, '用户名或密码错误', '');
                return response()->json($res, 401);
            }
            CacheService::setCache($key, $token, 3600);
        } catch (JWTException $e) {
            Log::error($e);
            $res = UtilService::format_data(self::AJAX_FAIL, '登录异常', '');
            return response()->json($res, 500);
        }

        $privileges = array();
        $roles = $user->roles;
        if($roles && count($roles) > 0){
            //所有权限
            foreach($roles as $role){
                if($role->id == 1){
                    //管理员，返回所有权限
                    $permissions = Permission::orderBy('sort', 'ASC')->get();
                }
                else {
                    $permissions = $role->permissions;
                }
                if($permissions && count($permissions) > 0) {
                    foreach ($permissions as $permission) {
                        $flag = $this->in_array($permission->id, $privileges);
                        if(!$flag) {
                            unset($permission->created_at);
                            unset($permission->updated_at);
                            unset($permission->deleted_at);
                            $privileges[] = $permission;
                        }
                    }
                }
            }

            //把有子节点没有选中的节点的父节点加入到菜单中来
            $tmp_privileges = $privileges;
            foreach ($tmp_privileges as $p){
                if($p->level != 1){
                    $path = $p->path;
                    $arr = explode('/', $path);
                    for ($i=0; $i<count($arr)-1; $i++) {
                        $this->loopParent($arr[$i], $privileges);
                    }
                }
            }
        }
        OperateLog::create([
            "user_id" => $user->id,
            "description"=> "登录管理后台"
        ]);
        $key = md5($token);
        event( new UserLoginEvent($user, $key)); //触发登录事件并广播
        $res = UtilService::format_data(self::AJAX_SUCCESS, '登录成功', compact('token', 'user', 'privileges'));
        return response()->json($res);
    }

    private function loopParent($id, &$privileges){
        $parent = Permission::where('id', $id)->first();
        $flag = $this->in_array($parent->id, $privileges);
        if(!$flag) {
            unset($parent->created_at);
            unset($parent->updated_at);
            unset($parent->deleted_at);
            $privileges[] = $parent;
        }
    }

    private function in_array($id, $array){
        $flag = false;
        if(count($array) > 0) {
            foreach ($array as $item) {
                if ($item->id == $id) {
                    $flag = true;
                    break;
                }
            }
        }

        return $flag;
    }

    /**
     * @SWG\Get(
     *     path="/api/auth/logout",
     *     tags={"auth api"},
     *     operationId="",
     *     summary="退出登录",
     *     description="使用说明：退出登录",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function logout()
    {
        try {
            $user = auth()->user();
            if($user && isset($user->mobile)) {
                $key = $this->getKey($user->mobile);
                CacheService::clearCache($key);
            }

            auth()->logout();
            $res = UtilService::format_data(self::AJAX_SUCCESS, '退出成功', '');
            return response()->json($res);
        } catch (Exception $e) {
            Log::error($e);
            $res = UtilService::format_data(self::AJAX_FAIL, '退出异常', '');
            return response()->json($res);
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/auth/refresh",
     *     tags={"auth api"},
     *     operationId="",
     *     summary="刷新token",
     *     description="使用说明：刷新token",
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
    public function refresh(){
        $user = auth()->user();
        $token = auth()->refresh();

        if($user) {
            $key = $this->getKey($user->mobile);
            CacheService::setCache($key, $token, 3600);
        }

        $res = UtilService::format_data(self::AJAX_SUCCESS, '刷新成功', compact('token'));
        return response()->json($res);
    }
}

/**
 * @SWG\Definition(
 *     definition="Privileges",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/Permission")
 * )
 */
