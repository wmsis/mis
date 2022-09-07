<?php
/**
* 测试用
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use HistorianService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use UtilService;
use Log;
//use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class HistorianController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function tags()
    {
        $rtn = HistorianService::tags();
        dd($rtn);
    }

    public function tagslist()
    {
        //$user = new UserRepository();
        //$lists = $user->all();
        $user = DB::table('orgnization')->where('id', 1)->first();
        dd($user);
        //phpinfo();
    }

    public function rawData()
    {

    }

    public function InterpolateData()
    {

    }

    public function currentData()
    {

    }

    public function CalculateData()
    {

    }

    public function trendData()
    {

    }

    /**
     * [encrypt aes加密]
     * @param    [type]                   $input [要加密的数据]
     * @param    [type]                   $key   [加密key]
     * @return   [type]                          [加密后的数据]
     */
    public function encrypt($input, $key)
    {
        $key = $this->_sha1prng($key);
        $iv = '';
        $data = openssl_encrypt($input, 'AES-128-ECB', $key, OPENSSL_RAW_DATA, $iv);
        $data = base64_encode($data);
        return $data;
    }

    /**
     * [decrypt aes解密]
     * @param    [type]                   $sStr [要解密的数据]
     * @param    [type]                   $sKey [加密key]
     * @return   [type]                         [解密后的数据]
     */
    public function decrypt($sStr, $sKey)
    {
        $sKey = $this->_sha1prng($sKey);
        $iv = '';
        $decrypted = openssl_decrypt(base64_decode($sStr), 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    /**
     * SHA1PRNG算法
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    private function _sha1prng($key)
    {
        return substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
    }
}
