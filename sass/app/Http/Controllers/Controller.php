<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use UtilService;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const AJAX_SUCCESS = 0;
    const AJAX_FAIL = -1;
    const AJAX_NO_DATA = -2;
    const AJAX_SUCCESS_MSG = '操作成功';
    const AJAX_FAIL_MSG = '操作失败';
    const AJAX_NO_DATA_MSG = '没有数据';
    const AJAX_ILLEGAL_MSG = '非法操作';
    const AJAX_GROUP_ROLE_MSG = '不能编辑集团角色';

    protected $orgnization;
    protected $mongo_conn;

    public function __construct(){
        $user = auth('api')->user();
        if($user && isset($user->last_login_orgnization) && $user->last_login_orgnization){
            $this->orgnization = Orgnization::find($user->last_login_orgnization);

            $domain = $_SERVER['HTTP_HOST'];
            $third = UtilService::third_domain($domain);
            if($third && strpos($domain, '10.99.99.88') === false){ //没查询到10.99.99.88  排除测试环境
                $tenement = DB::connection('mysql_mis')->table('tenement')->where('code', $third)->first();
                $this->mongo_conn = $tenement && isset($tenement->code) ? $tenement->code . '_mongo': 'wmhb_mongo';
            }
            else{
                $this->mongo_conn = 'wmhb_mongo';
            }
        }
    }
}
