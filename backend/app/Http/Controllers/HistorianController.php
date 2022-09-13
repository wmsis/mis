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
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Mongo\HistorianData;
use App\Models\Factory\GrabGarbage;  //电厂数据模型
use App\Models\SIS\WeighBridgeFormat;

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
        //$obj = (new GrabGarbage())->setConnection('likeshop');
        //$obj = new GrabGarbage();
        //$list = $obj->all();
        //dd($list[0]->name);
        //$user = new UserRepository();
        //$lists = $user->all();
        //$user = DB::table('orgnization')->where('id', 1)->first();
        //dd($user);
        phpinfo();

        //插入
        // $mongo = HistorianData::create([
        //     'cn_name' => '小李子',
        //     'value' => 20
        // ]);

        //查询
        // $info = HistorianData::first()->toArray();//单条查询
        // dd($info);
        //$info = HistorianData::where('_id','6317e9f64116000013006fa3')->get()->toArray();//单条查询
        //dd($info);
        // $list = HistorianData::get()->toArray();//多条查询
        //
        // //删除
        // $delete = HistorianData::where('_id','6139c4873f3fd3498c0001b4')->delete();
        // var_dump($delete);//返回"int(1)"
        //
        // //更新
        // $update = HistorianData::where('_id', '6139bf1cad844ba5a13d67c4')->update(['cn_name'=>'小小潘','value'=>19]);
        // var_dump($update);
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
