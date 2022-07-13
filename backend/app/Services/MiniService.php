<?php

namespace App\Services;
use UtilService;
use Illuminate\Support\Facades\Cache;
use Redis;

/**
 * 微信小程序
 */

class MiniService
{
    public function getOpenidAndSessionkey($code){
        $appid = config('mini.appid');
        $appsecret = config('mini.appsecret');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=authorization_code";

        $result = UtilService::curl_get($url);
        if($result&& isset($result['openid'])){
            return $result;
        }
        else{
            return null;
        }
    }

    public function getAccessToken($appid, $appsecret) {
        $key = md5($appid.'access_token');
        $data = Redis::get($key);
        if (!$data) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $result = UtilService::curl_get($url);
            if($result && isset($result['access_token'])){
                //请求接口成功
                $accessToken = $result['access_token'];
                $param = array(
                    "access_token" => $result['access_token'],
                    "expire_time" => time() + 6000
                );
                Redis::setex($key, 6000, json_encode($param));
            }
            else{
                //请求接口失败
                $accessToken = null;
            }
        }
        else {
            //accesstoken有效
            $data = json_decode($data, true);
            $accessToken = $data['access_token'];
        }

        return $accessToken;
    }

    public function sendTemplateMessage($appid, $appsecret, $postData){
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $access_token;
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

    public function sendUniformMessage($appid, $appsecret, $postData){
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?access_token=' . $access_token;
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

    private function json_encode_ex($value)
    {
        if (version_compare(PHP_VERSION,'5.4.0','<'))
        {
            $str = $this->my_encode_json($value);
            return $str;
        }
        else
        {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }

    //5.3之前中文转码
    private function my_encode_json($str) {
        return urldecode(json_encode($this->my_url_encode($str)));
    }

    private function my_url_encode($str) {
        if(is_array($str)) {
            foreach($str as $key=>$value) {
                $str[urlencode($key)] = $this->my_url_encode($value);
            }
        } else {
            $str = urlencode($str);
        }

        return $str;
    }

    public function getVisitPage($appid, $appsecret)
    {
        $access_token = $this->getAccessToken($appid, $appsecret);
        if($access_token !== null) {
            $begin_date = date('Ymd', time() - 24*60*60);
            $end_date = date('Ymd', time() - 24*60*60);
            $postData = array(
                "begin_date"=> $begin_date,
                "end_date"=> $end_date
            );
            $url = "https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=" . $access_token;
            $postData = $this->json_encode_ex($postData);
            $return = UtilService::curl_post($url, $postData);
            return $return;
        }
        else{
            return false;
        }
    }
}