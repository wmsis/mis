<?php
/**
* 微信小程序控制器
*
* 微信小程序相关接口
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UtilService;
use MiniService;   //引入 MiniService 门面
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Mini;

class MiniController extends Controller
{

    /**
     * 做活动用
     * 小程序login
     */
    public function wxlogin(Request $request) {
        $code = $request->input('code');
        $result = MiniService::getOpenidAndSessionkey($code);
        if($result && isset($result['openid']) && isset($result['session_key'])){
            $third_session = md5($result['openid'].'lucky');
            $key = $third_session;
            $param = array(
                "openid" => $result['openid'],
                "session_key" => $result['session_key']
            );

            if (Cache::has($key)) {
                Cache::forget($key);
            }

            //将session_key写进缓存，add 方法只会在缓存项不存在的情况下添加数据到缓存（分钟数）
            $res = Cache::add($key, $param, 60 * 6);
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', ['third_session'=>$third_session]);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_NO_DATA, '请求失败', '');
        }
    }

    /**
     * 从缓存中读取openid和sessionkey
     */
    private function getSessionByKey($third_session) {
        $res = Cache::get($third_session);
        if($res){
            return $res;
        }
        else{
            return null;
        }
    }
}
