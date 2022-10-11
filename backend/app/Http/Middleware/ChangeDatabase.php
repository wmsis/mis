<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use UtilService;
use MyCacheService;
use Log;
use App\Models\System\Tenement;

class ChangeDatabase
{
    /**
     * 更改默认租户数据库连接
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $default = 'mysql';
        $user = auth('admin')->user();
        if($user){
            $key = UtilService::getKey($user->id, 'TENEMENT');
            $tenement = MyCacheService::getCache($key);
            //$default = $request->tenement ? $request->tenement : 'mysql';  //tenement为租户编号code
            if($tenement && isset($tenement['code'])){
                $default = $tenement['code'];
            }
            else{
                $tenement = Tenement::first();
                if($tenement && isset($tenement['code'])){
                    //默认租户
                    $tenement = $tenement->toArray();
                    $default = $tenement['code'];
                    $expire = auth('admin')->factory()->getTTL() * 60;
                    MyCacheService::setCache($key, $tenement, $expire);
                }
                else{
                    $default = 'mysql';
                }
            }
        }

        Config::set('database.default', $default);
        return $next($request);
    }
}
