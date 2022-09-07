<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use UtilService;
use JWTAuth;
use App\Models\SIS\API;

class CheckPermission
{
    const AJAX_NO_AUTH = 99999;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //此处用auth()->user() 不会报token失效异常
        $userObj = JWTAuth::parseToken()->authenticate();
        $path = $request->path(); //接口路径
        $pattern = '/(\d+)/i'; //替换数字为{id}
        if(strpos($path, 'role') != false){
            $path = preg_replace($pattern, '{role}', $path);
        }
        elseif(strpos($path, 'admin') != false){
            $path = preg_replace($pattern, '{user}', $path);
        }
        elseif(strpos($path, 'pictxt') != false){
            $path = preg_replace($pattern, '{pictxt}', $path);
        }
        elseif(strpos($path, 'material') != false){
            $path = preg_replace($pattern, '{material}', $path);
        }
        elseif(strpos($path, 'member') != false){
            $path = preg_replace($pattern, '{member}', $path);
        }
        else{
            $path = preg_replace($pattern, '{id}', $path);
        }

        $flag = false;
        if($userObj->type == 'admin'){
            $flag = true;
        }
        else {
            $permissions = API::where('url', 'like', '%'.$path.'%')->get();
            foreach ($permissions as $permission) {
                //路径加权限ID连接，防止同一个路径多次定义
                if (Gate::allows($path.$permission->id, $permission)) {
                    $flag = true;
                    break;
                }
            }
        }

        if ($flag) {
            return $next($request);
        }
        else{
            return response(UtilService::format_data(self::AJAX_NO_AUTH, '没有权限', ''), 402);
        }
    }
}
