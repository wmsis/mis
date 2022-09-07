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
use App\Models\User;
use UtilService;
use CacheService;
use App\Models\OperateLog;
use App\Models\Permission;

/**
 * @OA\Info(
 *     version="2.0.0",
 *     title="MIS API 文档",
 *     description="伟明环保设备有限公司SIS系统 API 文档 api/documentation  php artisan l5-swagger:generate"
 * )
 */
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    private function getKey($mobile){
        return md5($mobile . '_TOKEN');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"权限认证auth"},
     *     operationId="login",
     *     summary="登录",
     *     description="使用说明：登录",
     *     @OA\Parameter(
     *         description="手机号码",
     *         in="query",
     *         name="mobile",
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
     *         @OA\Schema(
     *              @OA\Property(
     *                   property="privileges",
     *                   description="privileges",
     *                   allOf={
     *                        @OA\Schema(ref="#/definitions/Privileges")
     *                   }
     *              ),
     *             @OA\Property(
     *                   property="user",
     *                   description="user",
     *                   allOf={
     *                        @OA\Schema(ref="#/definitions/User")
     *                   }
     *              ),
     *              @OA\Property(
     *                   property="token",
     *                   @OA\Schema(
     *                        type="string"
     *                   ),
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
                JWTAuth::unsetToken();
            }

            if(!$user){
                $res = UtilService::format_data(self::AJAX_FAIL, '账号不存在', '');
                return response()->json($res, 401);
            }
            // attempt to verify the credentials and create a token for the user
            elseif (!$token = auth('api')->attempt($credentials)) {
                $res = UtilService::format_data(self::AJAX_FAIL, '用户名或密码错误', '');
                return response()->json($res, 401);
            }
            CacheService::setCache($key, $token, 3600);
        } catch (JWTException $e) {
            Log::error($e);
            $res = UtilService::format_data(self::AJAX_FAIL, '登录异常', '');
            return response()->json($res, 500);
        }

        $privileges = $this->get_permission($user); //获取用户菜单权限

        OperateLog::create([
            "user_id" => $user->id,
            "description"=> "登录管理后台"
        ]);
        $key = md5($token);
        event( new UserLoginEvent($user, $key)); //触发登录事件并广播
        return UtilService::format_data(self::AJAX_SUCCESS, '登录成功', compact('token', 'user', 'privileges'));
    }

    private function get_permission($user){
        $privileges = array();
        $roles = $user->roles;
        if($roles && count($roles) > 0){
            //所有权限
            foreach($roles as $role){
                if($role->type == 'admin'){
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
                    $this->loopParent($p->parent_id, $privileges);
                }
            }
        }

        $tree_privileges = array();
        foreach ($privileges as $key => $item) {
            if($item->level == 1){
                $tree_privileges[] = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'icon' => $item->icon,
                    'color' => $item->color,
                    'page_url' => $item->page_url,
                    'api_name' => $item->api_name,
                    'parent_id' => $item->parent_id,
                    'level' => $item->level,
                    'target' => '/' . $item->page_url,
                    'children' => $this->children($item, $privileges)
                );
            }
        }

        return $tree_privileges;
    }

    //递归求子集
    private function children($node, &$privileges){
        $rows = array();
        foreach ($privileges as $key => $item) {
            if($item->level > $node->level && $item->parent_id == $node->id){
                $rows[] = $item;
            }
        }

        $arr = [];
        foreach ($rows as $key => $item) {
            $arr[] = array(
                'id' => $item->id,
                'name' => $item->name,
                'icon' => $item->icon,
                'color' => $item->color,
                'page_url' => $item->page_url,
                'api_name' => $item->api_name,
                'parent_id' => $item->parent_id,
                'level' => $item->level,
                'target' => '/' . $item->page_url,
                'children' => $this->children($item, $privileges)
            );
        }

        return $arr;
    }

    //递归将没有全选的父节点加进权限
    private function loopParent($id, &$privileges){
        $parent = Permission::where('id', $id)->first();
        $flag = $this->in_array($parent->id, $privileges);
        if(!$flag) {
            unset($parent->created_at);
            unset($parent->updated_at);
            unset($parent->deleted_at);
            $privileges[] = $parent;
        }
        if($parent->parent_id){
            $this->loopParent($parent->parent_id, $privileges);
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
     * @OA\Get(
     *     path="/api/auth/logout",
     *     tags={"权限认证auth"},
     *     operationId="logout",
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
        try {
            $user = auth('api')->user();
            if($user && isset($user->mobile)) {
                $key = $this->getKey($user->mobile);
                CacheService::clearCache($key);
            }

            auth('api')->logout();
            $res = UtilService::format_data(self::AJAX_SUCCESS, '退出成功', '');
            return response()->json($res);
        } catch (Exception $e) {
            Log::error($e);
            $res = UtilService::format_data(self::AJAX_FAIL, '退出异常', '');
            return response()->json($res);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/refresh",
     *     tags={"权限认证auth"},
     *     operationId="refresh",
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
    public function refresh(){
        $user = auth('api')->user();
        $token = auth('api')->refresh();

        if($user) {
            $key = $this->getKey($user->mobile);
            CacheService::setCache($key, $token, 3600);
        }

        $res = UtilService::format_data(self::AJAX_SUCCESS, '刷新成功', compact('token'));
        return response()->json($res);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"权限认证auth"},
     *     operationId="me",
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
        try {
            $user = auth('api')->user();
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', array('user'=>$user));
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '操作异常', '');
        }
    }
}

/**
 * @OA\Definition(
 *     definition="Privileges",
 *     type="array",
 *     @OA\Items(ref="#/definitions/Permission")
 * )
 */
