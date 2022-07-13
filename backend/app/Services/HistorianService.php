<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use UtilService;

class HistorianService
{
    const AJAX_SUCCESS = 0;
    const AJAX_FAIL = -1;

    private function token()
    {
        $access_token = null;
        $auth = array(
            "username" => config('historian.username'),
            "password" => config('historian.password')
        );
        $key = md5($auth['username'] . 'wm_lucky' . $auth['password']);
        $has_cache_data = false;
        if (Cache::has($key)) {
            $val = Cache::get($key);
            $access_token = $val['access_token'];
            if ($val['expires_in'] > time()) {
                $has_cache_data = true;
            } else {
                Cache::forget($key);  //清除缓存
            }
        }

        if (!$has_cache_data) {
            $params = array(
                'verify' => false,
                'auth' => [
                    $auth['username'], $auth['password']
                ],
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded',
                ],
                "form_params" => [
                    "grant_type" => "client_credentials",   //send an application/x-www-form-urlencoded POST request
                ]
            );

            $url = config('historian.servername');
            $path = "/uaa/oauth/token";
            $response = \UtilService::client_post($url, $path, $params);
            $code = $response->getStatusCode();
            $body = $response->getBody();
            $data = $body->getContents();
            $json_data = json_decode($data, true);
            if ($response && $code && $code == 200 && $data && isset($json_data['access_token'])) {
                if (!Cache::has($key)) {
                    $v = array(
                        "access_token" => $json_data['access_token'],
                        "expires_in" => time() - 60 + (int)$json_data['expires_in']
                    );
                    Cache::add($key, $v, $json_data['expires_in']);
                }

                $access_token = $json_data['access_token'];
            }
        }

        return $access_token;
    }

    private function response($res)
    {
        $code = $res->getStatusCode();
        $body = $res->getBody();
        if ($res && $code && $code == 200) {
            $data = $body->getContents();
            $json_data = json_decode($data, true);
            return $json_data;
        } else if ($res && $code && $code == 401) {
            $auth = array(
                "username" => config('historian.username'),
                "password" => config('historian.password')
            );
            $key = md5($auth['username'] . 'wm_lucky' . $auth['password']);
            if (Cache::has($key)) {
                Cache::forget($key);  //清除缓存
            }
            return array(
                "code" => -1,
                "msg" => '登录失败'
            );
        } else {
            return array(
                "code" => -2,
                "msg" => '未知错误'
            );
        }
    }

    public function tags()
    {
        $access_token = $this->token();
        if ($access_token) {
            $url = config('historian.servername');
            $path = "/historian-rest-api/v1/tags";
            $params = array(
                'verify' => false,
                'headers' => [
                    "Authorization" => 'Bearer ' . $access_token
                ],
                "query" => [
                    "nameMask" => "*",
                    "maxNumber" => 15000
                ]
            );
            $res = UtilService::client_get($url, $path, $params);
            $rtn = $this->response($res);
            return UtilService::format_data(self::AJAX_SUCCESS, "获取成功", $rtn);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, "获取token失败", array());
        }
    }

    public function tagslist()
    {
        $access_token = $this->token();
        if ($access_token) {
            $url = config('historian.servername');
            $path = "/historian-rest-api/v1/tagslist";
            $params = array(
                'verify' => false,
                'headers' => [
                    "Authorization" => 'Bearer ' . $access_token
                ]
            );
            $res = UtilService::client_get($url, $path, $params);
            $rtn = $this->response($res);
            return UtilService::format_data(self::AJAX_SUCCESS, "获取成功", $rtn);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, "获取token失败", array());
        }
    }

    public function rawData($tagNames, $start, $end, $count)
    {
        $access_token = $this->token();
        if ($access_token) {
            $url = config('historian.servername');
            $path = "/historian-rest-api/v1/datapoints/raw/" . $start . '/' . $end . '/0/' . $count;
            $params = array(
                'verify' => false,
                'headers' => [
                    "Authorization" => 'Bearer ' . $access_token,
                ],
                'json' => [
                    'tagNames' => $tagNames
                ]
            );
            $res = UtilService::client_post($url, $path, $params);
            $rtn = $this->response($res);
            return UtilService::format_data(self::AJAX_SUCCESS, "获取成功", $rtn);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, "获取token失败", array());
        }
    }

    public function InterpolateData()
    {

    }

    public function currentData($tagNames)
    {
        $access_token = $this->token();
        if ($access_token) {
            $url = config('historian.servername');
            $path = "/historian-rest-api/v1/datapoints/currentvalue";
            $params = array(
                'verify' => false,
                'headers' => [
                    "Authorization" => 'Bearer ' . $access_token,
                ],
                'json' => [
                    'tagNames' => $tagNames
                ]
            );
            $res = UtilService::client_post($url, $path, $params);
            $rtn = $this->response($res);
            return UtilService::format_data(self::AJAX_SUCCESS, "获取成功", $rtn);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, "获取token失败", array());
        }
    }

    public function SampledData($tagNames, $start, $end, $count, $samplingMode, $calculationMode, $intervalMS = null)
    {
        $access_token = $this->token();
        if ($access_token) {
            $url = config('historian.servername');
            $path = "/historian-rest-api/v1/datapoints/sampled";
            $params = array(
                'verify' => false,
                'headers' => [
                    "Authorization" => 'Bearer ' . $access_token,
                ],
                'json' => [
                    'tagNames' => $tagNames,
                    'start' => $start,
                    'end' => $end,
                    'samplingMode' => $samplingMode,
                    'calculationMode' => $calculationMode,
                    'count' => $count
                ]
            );
            if ($intervalMS) {
                $params['json']['intervalMs'] = $intervalMS;
            }
            $res = UtilService::client_post($url, $path, $params);
            $rtn = $this->response($res);
            return UtilService::format_data(self::AJAX_SUCCESS, "获取成功", $rtn);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, "获取token失败", array());
        }
    }

    public function trendData()
    {

    }

    public function properties($tagName, $properties = null)
    {
        $access_token = $this->token();
        if ($access_token) {
            $url = config('historian.servername');
            $path = "/historian-rest-api/v1/tags/properties/" . $tagName;
            $params = array(
                'verify' => false,
                'headers' => [
                    "Authorization" => 'Bearer ' . $access_token,
                ],
            );
            if ($properties != null) {
                $params['json'] = $properties;
            }
            $res = UtilService::client_post($url, $path, $params);
            $rtn = $this->response($res);
            return UtilService::format_data(self::AJAX_SUCCESS, "获取成功", $rtn);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, "获取token失败", array());
        }
    }
}
