<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use UtilService;
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
        $default = $request->tenement ? $request->tenement : 'mysql';

        Config::set('database.default', $default);
        return $next($request);
    }
}
