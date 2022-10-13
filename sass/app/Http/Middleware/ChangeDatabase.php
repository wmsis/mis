<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use UtilService;
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
        $third = UtilService::third_domain($domain);
        if($third && strpos($domain, '10.99.99.88') === false && strpos($domain, '10.99.99.99') === false){ //没查询到10.99.99.88  排除测试环境
            if(strpos($domain, 'api') !== false){
                $third = str_replace('api', '', $third);
            }
            $tenement = DB::connection('mysql_mis')->table('tenement')->where('code', $third)->first();
            if(!$tenement || !isset($tenement->code)){
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
}
