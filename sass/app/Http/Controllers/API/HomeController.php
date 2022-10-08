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
     *     description="使用说明：首页发电量 上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标",
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
            'today' => array_merge($today_electricity, $today_handle_leachate, $today_weigh_bridge, $today_grab_garbage),
            'yestoday' => array_merge($yestoday_electricity, $yestoday_handle_leachate, $yestoday_weigh_bridge, $yestoday_grab_garbage),
            'month' => array_merge($month_electricity, $month_handle_leachate, $month_weigh_bridge, $month_grab_garbage),
        );
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $final);
    }

    /**
     * @OA\Get(
     *     path="/api/home/chart",
     *     tags={"首页home"},
     *     operationId="home-chart",
     *     summary="首页上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标趋势图",
     *     description="使用说明：首页发电量 上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标趋势图",
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
        $end_timestamp = time() - 24 * 60 * 60;
        $start = date('Y-m-d', $begin_timestamp);
        $end = date('Y-m-d', $end_timestamp);

        $month_grab_garbage = $grabGarbageObj->chartData($start, $end, $this->orgnization->code);  //垃圾入炉量
        $month_electricity = $electricityObj->chartData($start, $end, $this->orgnization->code);  //垃圾入库量
        $month_weigh_bridge = $weighBridgeObj->chartData($start, $end, $this->orgnization->code);  //垃圾入炉量

        //初始值
        for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
            $date = date('Y-m-d', $i);
            $final['grab_garbage'][$date] = 0;
            $final['weigh_bridge'][$date] = 0;
            foreach ($month_electricity as $key => $item) {
                $final[$key][$date] = 0;
            }
        }

        //赋值
        foreach ($final as $k1 => $typelist) {
            foreach ($typelist as $date => $value) {
                //抓斗
                if($k1 == 'grab_garbage'){
                    foreach ($month_grab_garbage as $key => $item) {
                        if($item['date'] == $date){
                            $final['grab_garbage'][$date] = (float)$item->val;
                            break;
                        }
                    }
                }
                //地磅
                elseif($k1 == 'weigh_bridge'){
                    foreach ($month_weigh_bridge as $key => $item) {
                        if($item['date'] == $date){
                            $final['weigh_bridge'][$date] = (float)$item->val;
                            break;
                        }
                    }
                }
                //发电量和上网电量
                else{
                    //循环发电量和上网电量  month_electricity包含发电量和上网电量
                    foreach ($month_electricity as $k2 => $itemlist) {
                        if($k1 == $k2){
                            //遍历其中一个
                            foreach ($itemlist as $k3 => $item) {
                                if($item->date == $date){
                                    $final[$k1][$date] = (float)$item->val;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $final);
    }
}
