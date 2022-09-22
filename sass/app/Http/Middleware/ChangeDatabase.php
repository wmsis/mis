<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use UtilService;
use CacheService;
use Log;

class ChangeDatabase
{
    const AJAX_SUCCESS = 0;
    const AJAX_FAIL = -1;
    const AJAX_NO_DATA = -2;

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
        $third = $this->third_domain($domain);
        Log::info('000000000000000');
        if($third && !strpos($domain, '10.99.99.88')){
            Log::info('1111111111111');
            $tenement = DB::connection('mysql_mis')->table('tenement')->where('code', $third)->first();
            if(!$tenement || !isset($tenement->code)){
                Log::info('22222222222');
                return response(UtilService::format_data(self::AJAX_FAIL, '商户不存在', ''), 200);
            }
            $default = $third;
        }
        else{
            $default = 'mysql';  //tenement为租户编号code
        }

        Config::set('database.default', $default);
        return $next($request);
    }

    /**
     * @notes 三级域名
     * @author cat
     * @date 2021/12/15 17:04
     */
    private function third_domain($domain = '')
    {
        $domainArr = explode('.', $domain);
        return array_shift($domainArr);
    }
}
