<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use UtilService;
use CacheService;
use Log;

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
            $key = $this->getKey($user->id, 'tenement');
            $tenement = CacheService::getCache($key);
            //$default = $request->tenement ? $request->tenement : 'mysql';  //tenement为租户编号code
            $default = $tenement ? $tenement['code'] : 'mysql';
        }

        Config::set('database.default', $default);
        return $next($request);
    }

    private function getKey($key, $constant){
        return md5($key . 'MIS' . $constant);
    }
}
