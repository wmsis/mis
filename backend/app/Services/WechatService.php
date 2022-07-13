<?php

namespace App\Services;
use UtilService;
use Illuminate\Support\Facades\Cache;
use Log;
use Redis;

/**
 * 微信公众号
 */

class WechatService
{
    const SNSAPI_BASE = 'snsapi_base';
    const SNSAPI_INFO = 'snsapi_userinfo';

    public function getPageUrl(){
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
        return $url;
    }

    /**
     * 官方access_token有效时间为7200S， 本例设置为6000S，过期后再去重新获取
     * 获取accesstoken...
     * 若正确返回access_token，否则返回null
     */
    public function getAccessToken($appid, $appsecret) {
        $key = md5($appid.'access_token');
        $data = Redis::get($key);
        if (!$data) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $result = UtilService::curl_get($url);
            if($result && isset($result['access_token'])){
                $param = array(
                    "access_token" => $result['access_token']
                );
                Redis::setex($key, 6000, json_encode($param));
                $access_token = $result['access_token'];
            }
            else{
                //请求接口失败
                $access_token = null;
            }
        }
        else {
            //accesstoken有效
            $data = json_decode($data, true);
            $access_token = $data['access_token'];
        }

        return $access_token;
    }

    /**
     * 官方jsapi_ticket有效时间为7200S， 本例设置为6000S，过期后再去重新获取
     * 获取jsapi_ticket...
     * 若正确返回jsapi_ticket，否则返回null
     */
    private function getJsapiTicket($appid, $appsecret) {
        $key = md5($appid.'jsapi_ticket');
        $data = Redis::get($key);
        if (!$data) {
            $access_token = $this->getAccessToken($appid, $appsecret);
            if($access_token !== null){
                $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
                $result = UtilService::curl_get($url);
                if($result && $result['errcode'] == 0 &&  isset($result['ticket'])){
                    //getJsapiTicket请求接口成功
                    $jsapi_ticket = $result['ticket'];
                    $param = array(
                        "jsapi_ticket" => $result['ticket'],
                        "expire_time" => time() + 6000
                    );
                    Redis::setex($key, 6000, json_encode($param));
                }
                else{
                    //getJsapiTicket请求接口失败
                    $jsapi_ticket = null;
                }
            }
            else{
                //accesstoken请求接口失败
                $jsapi_ticket = null;
            }
        }
        else {
            //jsapi_ticket有效
            $data = json_decode($data, true);
            $jsapi_ticket = $data['jsapi_ticket'];
        }

        return $jsapi_ticket;
    }

    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function jsapi($url, $appid, $appsecret){
        $jsapi_ticket = $this->getJsapiTicket($appid, $appsecret);
        if($jsapi_ticket) {
            $params = array();
            $params["url"] = $url;
            $params["timestamp"] = time();
            $noncestr = rand(1000000, 9999999);
            $params["noncestr"] = "$noncestr";
            $params["jsapi_ticket"] = $jsapi_ticket;
            ksort($params);
            $paramString = $this->ToUrlParams($params);
            $addrSign = sha1($paramString);

            $data = array(
                "signature" => $addrSign,
                "appId" => $appid,
                "timeStamp" => $params["timestamp"],
                "nonceStr" => $params["noncestr"]
            );
            return $data;
        }
        else{
            return null;
        }
    }

    /**
     * 生成网页授权跳转地址 ，用户同意授权，获取code...
     * @param $redirect_uri
     * @param $stateArr
     * @param $scope
     */
    private function oauth_url($appid, $redirect_uri, $state, $scope)
    {
        $urlparam = array(
            'appid=' . $appid,
            'redirect_uri=' . urlencode($redirect_uri),
            'response_type=code',
            'scope=' . $scope,
            'state=' . $state,
        );
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . join("&", $urlparam) . "#wechat_redirect";
    }

    /**
     * snsapi_base为scope发起的网页授权，是用来获取进入页面的用户的openid的 ...
     * @param $redirect_uri
     * @param $state
     * @return code ...
     */
    public function set_oauth_snsapi_base($appid, $redirect_uri, $state)
    {
        return $this->oauth_url($appid, $redirect_uri, $state, self::SNSAPI_BASE);
    }

    /**
     * snsapi_userinfo为scope发起的网页授权，是用来获取用户的基本信息的 ...
     * @param $redirect_uri
     * @param $state
     * @return code ...
     */
    public function set_oauth_snsapi_userinfo($appid, $redirect_uri, $state)
    {
        return $this->oauth_url($appid, $redirect_uri, $state, self::SNSAPI_INFO);
    }

    /**
     * 从微信API获取,通过code换取网页授权access_token等信息
     * @param $code string api返回的code
     * @return array access_token、expires_in、refresh_token、savetime等信息
     */
    public function getOauthInfoByCode($appid, $appsecret, $code)
    {
        $urlparam = array(
            'appid=' . $appid,
            'secret=' . $appsecret,
            'code=' . $code,
            'grant_type=authorization_code',
        );
        $apiUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?" . join("&", $urlparam);
        $json = file_get_contents($apiUrl);
        $result = json_decode($json);

        if(isset($result->errcode) && $result->errcode != 0){
            return null;
        }
        else{
            return $this->objectToArray($result);
        }
    }

    /**
     * 网页授权情况下  根据openid获取用户信息
     * @param string $openid 用户openid
     * @param string $access_token 通过code获取的accessToken
     * @return array用户基本数据
     */
    public function getUserInfoByOauth($openid, $access_token)
    {
        $urlparam = array(
            'access_token=' . $access_token,
            'openid=' . $openid,
            'lang=zh_CN',
        );
        $apiUrl = "https://api.weixin.qq.com/sns/userinfo?" . join("&", $urlparam);
        $json = file_get_contents($apiUrl);
        $result = json_decode($json);

        if(isset($result->errcode) && $result->errcode != 0){
            return null;
        }
        else{
            return $this->objectToArray($result);
        }
    }

    private function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)($this->objectToArray($v));
        }
        return $e;
    }

    public function isInWechat()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    public function createMenu($appid, $appsecret, $menu) {
        header("content-type:text/html; charset=UTF-8");
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token != null){
            //https请求
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
            $return = UtilService::curl_post($url, json_encode($menu, JSON_UNESCAPED_UNICODE));
            if(isset($return['errcode']) && $return['errcode'] != 0){
                $rtn = UtilService::format_data(-1, '生成失败', '错误代码为：'.$return['errcode']);
            }
            else{
                $rtn = UtilService::format_data(0, '菜单已生成', '');
            }
        }
        else{
            $rtn = UtilService::format_data(-1, '生成失败', 'access_token错误');
        }

        return $rtn;
    }

    public function uploadMediaFile($appid, $appsecret, $media_path, $timelong='limit', $type='image')
    {
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            if ($timelong == 'forever') {
                $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$access_token}&type=" . $type;
            } else {
                $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $access_token . "&type=" . $type;
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            if (class_exists('\CURLFile')) {
                $curlfile = curl_file_create(realpath($media_path));
            } else {
                $curlfile = '@' . realpath($media_path);
            }
            $postdata = array('media' => $curlfile);

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5); // 设置超时限制防止死循环
            curl_setopt($curl, CURLOPT_INFILESIZE, filesize($media_path));
            $return = curl_exec($curl);
            if (curl_errno($curl) == 0) {
                curl_close($curl);
                $result = json_decode($return, true);
                if (isset($result['errcode']) && $result['errcode'] != 0) {
                    return null;
                } else {
                    return $result['media_id'];
                }
            } else {
                return null;
            }
        }
        else{
            return null;
        }
    }

    public function sendCustomMessage($appid, $appsecret, $open_id, $msgtype, $content='', $media_id='', $materials=[]){
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            $domain = config('domain.api_url');
            $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $access_token;
            $postData = array(
                'touser' => $open_id,
                'msgtype' => $msgtype
            );

            if ($msgtype == 'text') {
                $postData['text'] = array(
                    'content' => $content
                );
            }
            else if ($msgtype == 'image') {
                $postData['image'] = array(
                    'media_id' => $media_id
                );
            }
            else if ($msgtype == 'video') {
                $postData['video'] = array(
                    'media_id' => $media_id
                );
            }
            else if ($msgtype == 'voice') {
                $postData['voice'] = array(
                    'media_id' => $media_id
                );
            }
            else if ($msgtype == 'news' && count($materials) > 0) {
                $articles = array();
                $articles[] = array(
                    "title"=> $materials[0]->title,
                    "description"=> $materials[0]->description,
                    "url"=> $materials[0]->url,
                    "picurl"=> $domain.$materials[0]->img
                );

                $postData['news'] = array(
                    "articles" => $articles
                );
            }

            $postData = $this->json_encode_ex($postData);
            $return = UtilService::curl_post($url, $postData);
            return $return;
        }
        else{
            return false;
        }
    }

    public function sendTemplateMessage($appid, $appsecret, $postData){
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
            $postData = $this->json_encode_ex($postData);
            $return = UtilService::curl_post($url, $postData);

            if (isset($return['errcode']) && $return['errcode'] != 0) {
                return false;
            } else {
                return true;
            }
        }
        else{
            return false;
        }
    }

    public function userInfoByOpenid($appid, $appsecret, $openid){
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?lang=zh_CN&openid=" . $openid . "&access_token=" . $access_token;
            $return = UtilService::curl_get($url);
            if (isset($return['errcode']) && $return['errcode'] != 0) {
                return null;
            } else {
                return $return;
            }
        }
        else{
            return null;
        }
    }

    /**
     * 对变量进行 JSON 编码
     * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
     * @return string 返回 value 值的 JSON 形式
     */
    private function json_encode_ex($value)
    {
        if (version_compare(PHP_VERSION,'5.4.0','<'))
        {
            $str = $this->encode_json($value);
            return $str;
        }
        else
        {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

    //5.3之前中文转码
    private function encode_json($str) {
        return urldecode(json_encode($this->url_encode($str)));
    }

    private function url_encode($str) {
        if(is_array($str)) {
            foreach($str as $key=>$value) {
                $str[urlencode($key)] = $this->url_encode($value);
            }
        } else {
            $str = urlencode($str);
        }

        return $str;
    }

    public function limit_qrcode($access_token, $type, $scene_str){
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;
        if($type == 'limit') {
            //临时二维码
            $params = <<<PARAM
    			    {
	    			    "expire_seconds": 7200,
	    			    "action_name": "QR_STR_SCENE",
	    			    "action_info":
	    			        {"scene":
	    			           {"scene_str": "$scene_str"}
    		                }
    		        }
PARAM;
        }
        else{
            //永久二维码
            $params = <<<PARAM
    			    {
	    			    "action_name": "QR_LIMIT_STR_SCENE",
	    			    "action_info":
	    			        {"scene":
	    			           {"scene_str": "$scene_str"}
    		                }
    		        }
PARAM;
        }

        $return = UtilService::curl_post($url, $params);
        if (isset($return['errcode']) && $return['errcode'] != 0) {
            return null;
        }
        else {
            $qrcode_img_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($return['ticket']);
            return $qrcode_img_url;
        }
    }
}
