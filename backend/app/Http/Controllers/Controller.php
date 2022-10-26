<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use UtilService;
use MyCacheService;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const AJAX_SUCCESS = 0;
    const AJAX_FAIL = -1;
    const AJAX_NO_DATA = -2;
    const AJAX_NO_DATA_MSG = '数据不存在';
    const AJAX_SUCCESS_MSG = '操作成功';
    const AJAX_FAIL_MSG = '操作失败';
    protected $mongo_conn;

    public function __construct(){
        $mongo_conn = 'wmhb_mongo';
        $user = auth('admin')->user();
        if($user){
            $key = UtilService::getKey($user->id, 'TENEMENT');
            $tenement = MyCacheService::getCache($key);
            if($tenement && isset($tenement['code'])){
                $mongo_conn = $tenement['code'] . '_mongo';
            }
        }

        $this->mongo_conn = $mongo_conn;
    }
}
