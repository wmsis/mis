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
        phpinfo();
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
}
