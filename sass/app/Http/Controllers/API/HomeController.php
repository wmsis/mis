<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\ElectricityDayData;
use App\Models\SIS\GrabGarbageDayData;
use App\Models\SIS\WeighBridgeDayData;
use App\Models\SIS\HistorianFormatData;
use App\Repositories\ElectricityDayDataRepository;
use App\Repositories\WeighBridgeDayDataReposotory;
use App\Repositories\GrabGarbageDayDataReposotory;
use UtilService;
use Log;

class HomeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/home/total",
     *     tags={"首页home"},
     *     operationId="home-total",
     *     summary="首页上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标",
     *     description="使用说明：首页上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function total(Request $request)
    {
        $electricityObj = new ElectricityDayDataRepository();
        $weighBridgeObj = new WeighBridgeDayDataReposotory();
        $grabGarbageObj = new GrabGarbageDayDataReposotory();


        //1、今日0点到现在累计值
        $start = date('Y-m-d');
        $end = $start;
        $today_electricity = $electricityObj->countData($start, $end, $this->orgnization->code);  //上网电量 厂用电量
        $today_weigh_bridge = $weighBridgeObj->countData($start, $end, $this->orgnization->code);  //垃圾入库量
        $today_grab_garbage = $grabGarbageObj->countData($start, $end, $this->orgnization->code);  //垃圾入炉量
        $today_handle_leachate = array(array(
            'cn_name'=>'渗沥液处理量',
            'en_name'=>'handle_leachate',
            'value'=>0,
            'messure' => '吨'
        )); //渗沥液处理量


        //2、昨日累计值
        $start = date('Y-m-d', time() - 24 * 60 * 60);
        $end = $start;
        $yestoday_electricity = $electricityObj->countData($start, $end, $this->orgnization->code);  //上网电量 厂用电量
        $yestoday_weigh_bridge = $weighBridgeObj->countData($start, $end, $this->orgnization->code);  //垃圾入库量
        $yestoday_grab_garbage = $grabGarbageObj->countData($start, $end, $this->orgnization->code);  //垃圾入炉量
        $yestoday_handle_leachate = array(array(
            'cn_name'=>'渗沥液处理量',
            'en_name'=>'handle_leachate',
            'value'=>0,
            'messure' => '吨'
        )); //渗沥液处理量


        //3、近30天（截止到今天凌晨0点）累计值
        $start = date('Y-m-d', time() - 30 * 24 * 60 * 60);
        $end = date('Y-m-d', time() - 24 * 60 * 60);
        $month_electricity = $electricityObj->countData($start, $end, $this->orgnization->code);  //上网电量 厂用电量
        $month_weigh_bridge = $weighBridgeObj->countData($start, $end, $this->orgnization->code);  //垃圾入库量
        $month_grab_garbage = $grabGarbageObj->countData($start, $end, $this->orgnization->code);  //垃圾入炉量
        $month_handle_leachate = array(array(
            'cn_name'=>'渗沥液处理量',
            'en_name'=>'handle_leachate',
            'value'=>0,
            'messure' => '吨'
        )); //渗沥液处理量



        $final = array(
            'today' => array_merge($today_electricity, $today_weigh_bridge, $today_grab_garbage),
            'yestoday' => array_merge($yestoday_electricity, $yestoday_weigh_bridge, $yestoday_grab_garbage),
            'month' => array_merge($month_electricity, $month_weigh_bridge, $month_grab_garbage),
        );
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/home/chart",
     *     tags={"首页home"},
     *     operationId="home-chart",
     *     summary="首页上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标趋势图",
     *     description="使用说明：首页上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标趋势图",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function chart(Request $request)
    {
        $final = [];
        $grabGarbageObj = new GrabGarbageDayDataReposotory();
        $electricityObj = new ElectricityDayDataRepository();
        $weighBridgeObj = new WeighBridgeDayDataReposotory();

        //近30天曲线图
        $begin_timestamp = time() - 30 * 24 * 60 * 60;
        $end_timestamp = time();
        $start = date('Y-m-d', $begin_timestamp);
        $end = date('Y-m-d', $end_timestamp);
        $datelist = array();
        for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
            $date = date('Y-m-d', $i);
            $datelist[$date] = 0; //初始值
        }

        $month_grab_garbage = $grabGarbageObj->chartData($start, $end, $this->orgnization->code);  //垃圾入炉量
        $month_electricity = $electricityObj->chartData($start, $end, $this->orgnization->code);  //用电量
        $month_weigh_bridge = $weighBridgeObj->chartData($start, $end, $this->orgnization->code);  //垃圾入库量

        //上网电量和厂用电量
        foreach ($month_electricity as $k1 => $itemlist) {
            //遍历其中一个
            $temp = array(
                'en_name' => $itemlist['en_name'],
                'cn_name' => $itemlist['cn_name'],
                'messure' => $itemlist['messure'],
                'datalist' => $datelist,
            );
            if($itemlist['datalist'] && count($itemlist['datalist']) > 0){
                for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                    $date = date('Y-m-d', $i);
                    foreach ($itemlist['datalist'] as $k2 => $item) {
                        if($item->date == $date){
                            //有当天数据
                            $temp['datalist'][$date] = (float)$item->val;
                            break;
                        }
                    }
                }
            }

            $final[] = $temp;
        }

        //垃圾入炉量
        $grab_garbage_datalist = $datelist;
        if($month_grab_garbage['datalist'] && count($month_grab_garbage['datalist']) > 0){
            for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                $date = date('Y-m-d', $i);
                foreach ($month_grab_garbage['datalist'] as $k2 => $item) {
                    $date = date('Y-m-d', $i);
                    if($item->date == $date){
                        $grab_garbage_datalist[$date] = (float)$item->val;
                        break;
                    }
                }
            }
        }

        $final[] = array(
            'en_name' => $month_grab_garbage['en_name'],
            'cn_name' => $month_grab_garbage['cn_name'],
            'messure' => $month_grab_garbage['messure'],
            'datalist' => $grab_garbage_datalist,
        );

        //垃圾入库量
        $weigh_bridge_datalist = $datelist;
        if($month_weigh_bridge['datalist'] && count($month_weigh_bridge['datalist']) > 0){
            for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                $date = date('Y-m-d', $i);
                foreach ($month_weigh_bridge['datalist'] as $k2 => $item) {
                    if($item->date == $date){
                        $weigh_bridge_datalist[$date] = (float)$item->val;
                        break;
                    }
                }
            }
        }

        $final[] = array(
            'en_name' => $month_weigh_bridge['en_name'],
            'cn_name' => $month_weigh_bridge['cn_name'],
            'messure' => $month_weigh_bridge['messure'],
            'datalist' => $weigh_bridge_datalist,
        );

        //渗沥液处理量
        // $handle_leachate_datalist = [];
        // for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
        //     $date = date('Y-m-d', $i);
        //     $handle_leachate_datalist[$date] = mt_rand(50,100);
        // }
        // $final[] = array(
        //     'en_name' => 'slycll',
        //     'cn_name' => '渗沥液处理量',
        //     'messure' => '吨',
        //     'datalist' => $handle_leachate_datalist,
        // );

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }
}
