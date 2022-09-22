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
        $server = $request->server();
        $domain = $server['HTTP_HOST'];
        Log::info('00000000000000000');
        Log::info($domain);
        $second = $this->secondary_domain($domain);
        Log::info('11111111111');
        Log::info($second);
        $default = $request->tenement ? $request->tenement : 'mysql';  //tenement为租户编号code
        Config::set('database.default', $default);
        return $next($request);
    }

    /**
     * @notes 二级域名
     * @author cat
     * @date 2021/12/15 17:04
     */
    public function secondary_domain($domain = '')
    {
        if('' == $domain){
            $domain = request()->domain();
            $domain = str_replace(request()->scheme() . '://', '', $domain);
        }
        $domainArr = explode('.', $domain);
        array_shift($domainArr);
        return implode('.',$domainArr);
    }

    /**
     * @notes 三级域名
     * @author cat
     * @date 2021/12/15 17:04
     */
    public function third_domain($domain = '')
    {
        if('' == $domain){
            $domain = request()->domain();
            $domain = str_replace(request()->scheme() . '://', '', $domain);
        }
        $domainArr = explode('.', $domain);
        return array_shift($domainArr);
    }
}
