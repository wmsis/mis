<?php

namespace App\Services;

use Log;
use GuzzleHttp\Client;

class UtilService
{
    private $privateKey = "233f4def5c875875";
    private $iv = "233f4def5c875875";

    public function curl_post($url, $data){
        $curl = curl_init(); // 启动一个CURL会话

        if (isset($data['header']) && !empty($data['header'])) {
            $headers = $data['header'];
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            unset($data['header']);
        }

        if (isset($data['type'])) {
            if ($data['type'] == 'json') {
                unset($data['type']);
                $data = json_encode($data);
            }
            elseif ($data['type'] == 'x-www-form-urlencoded') {
                unset($data['type']);
                $data = http_build_query($data);
            }
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作

        if (curl_errno($curl)) {
            curl_close($curl); // 关闭CURL会话
            return null;
        }
        else{
            curl_close($curl); // 关闭CURL会话
            $result = json_decode($tmpInfo, true);
            return $result; // 返回数据
        }
    }

    public function curl_post_new($url, $data, $headers, $body_type)
    {
        $curl = curl_init(); // 启动一个CURL会话

        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        if ($body_type) {
            if ($body_type == 'json') {
                $data = json_encode($data);
            }
            elseif ($body_type == 'x-www-form-urlencoded') {
                $data = http_build_query($data);
            }
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        Log::info('RESULT: ' . var_export($tmpInfo, true));
        if (curl_errno($curl)) {
            Log::info('ERROR: ' . curl_error($curl));
            curl_close($curl); // 关闭CURL会话
            return null;
        }
        else {
            curl_close($curl); // 关闭CURL会话
            $result = json_decode($tmpInfo, true);
            return $result; // 返回数据
        }
    }

    public function curl_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// 要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	// https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output, true);
        return $result;
    }

    public function client_post($base_url, $path, $params){
        $client = new Client([
            'base_uri' => $base_url
        ]);
        $return = $client->request('POST', $path, $params);
        return $return;
    }

    public function client_get($base_url, $path, $params){
        $client = new Client([
            'base_uri' => $base_url
        ]);
        $return = $client->request('GET', $path, $params);
        return $return;
    }

    public function format_data($code, $message, $data=array())
    {
        if(!is_numeric($code)){
            return '';
        }
        else{
            $result = array(
                "code" => $code,
                "message" => $message,
                "data" => $data
            );

            return $result;
        }
    }

    public function millisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    public function domain(){
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        return $url;
    }

    public function aesencrypt($str){
        $data = json_encode($str);
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $data, MCRYPT_MODE_CBC, $this->iv));

        return $encrypted;
    }

    public function aesdecrypt($str){
        $encryptedData = base64_decode($str);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $encryptedData, MCRYPT_MODE_CBC, $this->iv);
        $decrypted = rtrim($decrypted,"\0");
        $decrypted = json_decode($decrypted);

        return $decrypted;
    }

    public function random_str($length) {
        $arr = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            1, 2, 3, 4, 5, 6, 7, 8, 9, 0,
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];

        $arr_length = count($arr);
        $str = '';
        for ($i = 0; $i < $length; $i++)
        {
            $rand = rand (0, $arr_length-1);
            $str .= $arr[$rand];
        }

        return $str;
    }
}