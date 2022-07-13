<?php

namespace App\Http\Middleware;

use Closure;

class AccessControlAllowOrigin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $allow_origin = [
            'http://localhost',
            'http://localhost:8081',
            'http://192.168.100.127',
            'http://192.168.100.127:8081',
            'http://192.168.100.160',
            'http://192.168.100.160:8081',
            'http://127.0.0.1',
            'http://127.0.0.1:8081',
            'http://www.sis.com',
            'http://www.yqsis.com'
        ];

        //if (in_array($origin, $allow_origin)) {
            //$response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
            $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, PUT, OPTIONS');
            $response->header('Access-Control-Allow-Credentials', 'true');
       // }
        return $response;
    }
}
