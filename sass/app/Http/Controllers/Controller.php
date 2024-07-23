<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use UtilService;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const AJAX_SUCCESS = 0;
    const AJAX_FAIL = -1;
    const AJAX_NO_DATA = -2;
    const AJAX_SUCCESS_MSG = '操作成功';
    const AJAX_FAIL_MSG = '操作失败';
    const AJAX_NO_DATA_MSG = '数据不存在';
    const AJAX_ILLEGAL_MSG = '非法操作';
    const AJAX_GROUP_ROLE_MSG = '不能编辑集团角色';

    protected $orgnization;
    protected $tenement_conn;
    protected $mongo_conn;

    public function __construct(){
        $user = auth('api')->user();
        Log::info("000000000000");
        if($user && isset($user->last_login_orgnization) && $user->last_login_orgnization){
            Log::info("1111111111111111");
            $this->orgnization = Orgnization::find($user->last_login_orgnization);

            $domain = $_SERVER['HTTP_HOST'];
            Log::info($domain);
            $third = UtilService::third_domain($domain);
            if($third && strpos($domain, '10.99.99.88') === false && strpos($domain, '10.99.99.99') === false){ //没查询到10.99.99.88  排除测试环境
                Log::info("22222222222");
                Log::info($third);
                $tenement = DB::connection('mysql_mis')->table('tenement')->where('code', $third)->first();
                $this->mongo_conn = $tenement && isset($tenement->code) ? $tenement->code . '_mongo': 'wmhb_mongo';
                $this->tenement_conn = $tenement && isset($tenement->code) ? $tenement->code: 'wmhb';
            }
            else{
                Log::info("3333333333333333");
                $this->mongo_conn = 'wmhb_mongo';
                $this->tenement_conn = 'wmhb';
            }
        }
        else{
            Log::info("99999999999999");
        }
    }
}
