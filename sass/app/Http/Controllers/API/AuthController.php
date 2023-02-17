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
use MyCacheService;
use App\Models\OperateLog;
use App\Models\Permission;
use App\Models\SIS\Orgnization;
use App\Models\SIS\SysUserMap;
use Illuminate\Support\Facades\DB;

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
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'loginBySystem']]);
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
            $key = UtilService::getKey($params['mobile'], 'TOKEN');
            $current_token = MyCacheService::getCache($key);
            if($current_token){
                //将老token加入黑名单
                JWTAuth::setToken($current_token)->invalidate();
            }

            if(!$user){
                return UtilService::format_data(self::AJAX_FAIL, '用户不存在', '');
            }
            // attempt to verify the credentials and create a token for the user
            elseif (!$token = auth('api')->attempt($credentials)) {
                return UtilService::format_data(self::AJAX_FAIL, '用户名或者密码错误', '');
            }
            else{
                if($user->type == 'admin'){
                    $user['type_name'] = config('standard.user.admin');
                }
                elseif($user->type == 'group'){
                    $user['type_name'] = config('standard.user.group');
                }
                elseif($user->type == 'webmaster'){
                    $user['type_name'] = config('standard.user.webmaster');
                }
                elseif($user->type == 'instation'){
                    $user['type_name'] = config('standard.user.instation');
                }
                else{
                    $user['type_name'] = '';
                }

                //用户所在组织
                if($user->type == 'admin'){
                    $orgnizations = Orgnization::where('level', 2)->orderBy('sort', 'asc')->get();
                }
                else{
                    $orgnizations = $user->orgnizations()->where('level', 2)->orderBy('sort', 'asc')->get();
                }
                $user->orgnizations = $orgnizations;
            }
            MyCacheService::setCache($key, $token, 3600);

            //更新映射表中的token
            $map = SysUserMap::where('basic_conn_name', 'mysql_sis')->where('basic_user_id', $user->id)->first();
            if($map){
                $map->basic_token = $token;
                $map->save();
            }
        } catch (JWTException $e) {
            Log::error($e);
            $res = UtilService::format_data(self::AJAX_FAIL, $e->getMessage(), '');
            return response()->json($res, 500);
        }

        //$privileges = $this->get_permission($user); //获取用户菜单权限

        OperateLog::create([
            "user_id" => $user->id,
            "description"=> "登录管理后台"
        ]);
        $key = md5($token);
        event( new UserLoginEvent($user, $key)); //触发登录事件并广播
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, compact('token', 'user'));
    }

    private function get_permission($user){
        $privileges = array();
        $roles = $user->roles;
        if($roles && count($roles) > 0){
            //所有权限
            foreach($roles as $role){
                if($role->type == 'admin' || $role->type == 'group'){
                    //管理员，返回所有权限
                    $permissions = Permission::orderBy('sort', 'ASC')->get();
                }
                else {
                    $permissions = $role->permissions()->orderBy('sort', 'ASC')->get();
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
                    'type' => $item->type,
                    'title' => $item->name,
                    'sort' => $item->sort,
                    'is_show' => $item->is_show,
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
                'type' => $item->type,
                'title' => $item->name,
                'sort' => $item->sort,
                'is_show' => $item->is_show,
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
    public function logout(Request $request)
    {
        try {
            $user = auth('api')->user();
            if($user && isset($user->mobile)) {
                $key = UtilService::getKey($user->mobile, 'TOKEN');
                MyCacheService::clearCache($key);

                $server = $request->server();
                $domain = $server['HTTP_HOST'];
                $third = UtilService::third_domain($domain);
                $key_orgnization = UtilService::getKey($user->id, 'ORGNIZATION' . $third);
                MyCacheService::clearCache($key_orgnization);

                $user->last_login_orgnization = NULL;
                $user->save();
            }

            auth('api')->logout();
            $res = UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
            return response()->json($res);
        } catch (Exception $e) {
            Log::error($e);
            $res = UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
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
    public function refresh(Request $request){
        $user = auth('api')->user();
        $token = auth('api')->refresh();

        if($user) {
            //token
            $expire = auth('api')->factory()->getTTL() * 60;
            $key = UtilService::getKey($user->mobile, 'TOKEN');
            MyCacheService::setCache($key, $token, $expire);

            //orgnization
            $server = $request->server();
            $domain = $server['HTTP_HOST'];
            $third = UtilService::third_domain($domain);
            $key_orgnization = UtilService::getKey($user->id, 'ORGNIZATION' . $third);
            $data = Orgnization::find($user->last_login_orgnization)->toArray();
            MyCacheService::setCache($key_orgnization, $data, $expire);

            //更新映射表中的token
            $map = SysUserMap::where('basic_conn_name', 'mysql_sis')->where('basic_user_id', $user->id)->first();
            if($map){
                $map->basic_token = $token;
                $map->save();
            }
        }

        $res = UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, compact('token'));
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
            if($user->type == 'admin'){
                $user['type_name'] = config('standard.user.admin');
            }
            elseif($user->type == 'group'){
                $user['type_name'] = config('standard.user.group');
            }
            elseif($user->type == 'webmaster'){
                $user['type_name'] = config('standard.user.webmaster');
            }
            elseif($user->type == 'instation'){
                $user['type_name'] = config('standard.user.instation');
            }
            else{
                $user['type_name'] = '';
            }

            //用户所在组织
            if($user->type == 'admin'){
                $orgnizations = Orgnization::where('level', 2)->orderBy('sort', 'asc')->get();
            }
            else{
                $orgnizations = $user->orgnizations()->where('level', 2)->orderBy('sort', 'asc')->get();
            }

            $privileges = $this->get_permission($user); //获取用户菜单权限
            $switch = SysUserMap::where('basic_conn_name', 'mysql_sis')->where('basic_user_id', $user->id)->first();

            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, compact('user', 'privileges', 'switch', 'orgnizations'));
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/switch",
     *     tags={"权限认证auth"},
     *     operationId="switch",
     *     summary="切换到报表系统",
     *     description="使用说明：切换到报表系统",
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
     *         name="userid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="登录URL",
     *         in="query",
     *         name="url",
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
    public function switch(Request $request)
    {
        $user = auth('api')->user();
        $key = UtilService::getKey($user->mobile, 'TOKEN');
        $token = MyCacheService::getCache($key);
        $userid = $request->input('userid');
        $url = $request->input('url');
        try {
            $data = array(
                'type'=>'x-www-form-urlencoded',
                'system_token'=>$token,
                'userid'=>$userid
            );

            Log::info('99999999999999');
            Log::info(var_export($url, true));
            Log::info(var_export($data, true));

            $res = UtilService::curl_post($url, $data);
            Log::info('000000000000');
            Log::info(var_export($res, true));
            if($res && $res['code'] == 0){
                Log::info('11111111111111');
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res['data']);
            }
            else{
                Log::info('222222222222222');
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '请先关联用户');
            }
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    public function loginBySystem(Request $request)
    {
        $system_token = $request->input('system_token');
        $userid = $request->input('userid');
        try {
            $map = SysUserMap::where('target_token', $system_token)->where('basic_user_id', $userid)->first();
            $user = User::where('id', $userid)->first();
            if($map && $user){
                $key = UtilService::getKey($user->mobile, 'TOKEN');
                $current_token = MyCacheService::getCache($key);
                if($current_token){
                    //将老token加入黑名单
                    JWTAuth::setToken($current_token)->invalidate(true);
                }
                $token = auth()->tokenById($user->id);

                //更新映射表中的token
                $map = SysUserMap::where('basic_sys_name', 'mysql_sis')->where('basic_user_id', $user->id)->first();
                if($map){
                    $map->basic_token = $token;
                    $map->save();
                }
                MyCacheService::setCache($key, $token, 3600);
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, compact('user', 'token'));
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '请先关联用户');
            }
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
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
