<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\ElectricityDayData;
use App\Models\SIS\GrabGarbageDayData;
use App\Models\SIS\WeighBridgeDayData;
use App\Models\SIS\HistorianFormatData;

class HomeController extends Controller
{
    /*
    * 首页发电量 上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标
    */
    public function total(Request $request)
    {
        //1、今日0点到现在累计值
        

        //2、昨日累计值

        //3、近30天（截止到今天凌晨0点）累计值
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', '');
    }

    public function chart(Request $request)
    {
        //近30天曲线图
        $cn_name = $request->input('cn_name');
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', '');
    }
}
