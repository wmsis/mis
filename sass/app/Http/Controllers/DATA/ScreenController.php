<?php

namespace App\Http\Controllers\DATA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ElectricityDayDataRepository;
use App\Repositories\WeighBridgeDayDataReposotory;
use App\Repositories\GrabGarbageDayDataReposotory;
use App\Repositories\DcsStandardRepository;
use App\Models\SIS\Orgnization;
use UtilService;
use Log;

class ScreenController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/screen/total",
     *     tags={"大数据大屏screen"},
     *     operationId="screen-total",
     *     summary="上网电量 厂用电量等汇总指标",
     *     description="使用说明：厂用电量等汇总指标",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="电厂ID 多个用英文逗号分割",
     *         in="query",
     *         name="factory_ids",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=false,
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
        //获取接收的参数
        $factory_ids = $request->input('factory_ids');
        $start = $request->input('start');
        $end = $request->input('end');

        //初始化参数
        $electricityObj = new ElectricityDayDataRepository();
        $grabGarbageObj = new GrabGarbageDayDataReposotory();
        $weighBridgeObj = new WeighBridgeDayDataReposotory();
        $begin_timestamp = $start ? strtotime($start) : strtotime(date('Y-m-d') . " 00:00:00");
        $end_timestamp = $end ? strtotime($end) : strtotime(date('Y-m-d') . " 23:59:59");
        $start_date = date('Y-m-d', $begin_timestamp);
        $end_date = date('Y-m-d', $end_timestamp);

        //获取电厂组织
        if($factory_ids == '' || $factory_ids == 'all'){
            $factories = Orgnization::where('level', 2)->get();
        }
        else{
            $idarr = explode(',', $factory_ids);
            $factories = Orgnization::where('level', 2)->whereIn('id', $idarr)->get();
        }

        $datalist = array(
            "electricity" => [],
            "grab_garbage" => [],
            "weigh_bridge" => []
        );
        if($factories && count($factories) > 0){
            $electricity_keys = [];
            $grab_garbage_keys = [];
            $weigh_bridge_keys = [];
            //获取各个电厂的数据
            foreach ($factories as $kf => $factory) {
                if($factory->code){
                    $datalist['electricity'][$factory->code] = $electricityObj->countData($start_date, $end_date, $factory->code);  //用电量
                    $datalist['grab_garbage'][$factory->code] = $grabGarbageObj->countData($start_date, $end_date, $factory->code);  //垃圾入炉量
                    $datalist['weigh_bridge'][$factory->code] = $weighBridgeObj->countData($start_date, $end_date, $factory->code);  //垃圾入库量
                }
            }

            $final = [];

            //各个电厂数据累计，键值相同的累计
            $i=0;
            foreach($datalist['electricity'] as $factory=>$factory_values){
                foreach ($factory_values as $k2 => $dcs_value) {
                    if($i == 0){
                        $final[$dcs_value['en_name']] = $dcs_value;
                    }
                    else{
                        $final[$dcs_value['en_name']]['value'] += $dcs_value['value'];
                    }
                }
                $i++;
            }

            $j=0;
            foreach($datalist['grab_garbage'] as $k1=>$factory_values){
                foreach ($factory_values as $k2 => $dcs_value) {
                    if($j == 0){
                        $final[$dcs_value['en_name']] = $dcs_value;
                    }
                    else{
                        $final[$dcs_value['en_name']]['value'] += $dcs_value['value'];
                    }
                }
                $j++;
            }

            $k=0;
            foreach($datalist['weigh_bridge'] as $k1=>$factory_values){
                foreach ($factory_values as $k2 => $dcs_value) {
                    if($k == 0){
                        $final[$dcs_value['en_name']] = $dcs_value;
                    }
                    else{
                        $final[$dcs_value['en_name']]['value'] += $dcs_value['value'];
                    }
                }
                $k++;
            }

            $final = array_values($final);
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/screen/chart",
     *     tags={"大数据大屏screen"},
     *     operationId="screen-chart",
     *     summary="上网电量 厂用电量等指标趋势图",
     *     description="使用说明：上网电量 厂用电量等指标趋势图",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="电厂ID 多个用英文逗号分割",
     *         in="query",
     *         name="factory_ids",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=false,
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
        //获取接收的参数
        $factory_ids = $request->input('factory_ids');
        $start = $request->input('start');
        $end = $request->input('end');

        //初始化参数
        $final = [];
        $electricityObj = new ElectricityDayDataRepository();
        $grabGarbageObj = new GrabGarbageDayDataReposotory();
        $weighBridgeObj = new WeighBridgeDayDataReposotory();
        $begin_timestamp = $start ? (strtotime($start)-24*60*60) : time() - 15 * 24 * 60 * 60;
        $end_timestamp = $end ? strtotime($end) : time();
        $start_date = date('Y-m-d', $begin_timestamp);
        $end_date = date('Y-m-d', $end_timestamp);

        //获取电厂组织
        if($factory_ids == '' || $factory_ids == 'all'){
            $factories = Orgnization::where('level', 2)->get();
        }
        else{
            $idarr = explode(',', $factory_ids);
            $factories = Orgnization::where('level', 2)->whereIn('id', $idarr)->get();
        }

        if($factories && count($factories) > 0){
            $month_electricity = [];
            $season_electricity = [];
            $month_grab_garbage = [];
            $month_weigh_bridge = [];
            $type_weigh_bridge = [];
            $season_weigh_bridge = [];
            $datelist = array();
            for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                $date = date('Y-m-d', $i);
                $datelist[$date] = 0; //初始值
            }

            //获取各个电厂的曲线数据
            $the_day_after_start_date = date('Y-m-d', $begin_timestamp+24*60*60);
            foreach ($factories as $kf => $factory) {
                if($factory->code){
                    $year = date("Y");
                    $month_electricity[$factory->code] = $electricityObj->chartData($start_date, $end_date, $factory->code);   //用电量
                    $season_electricity[$factory->code] = $electricityObj->season($year, $factory->code);                      //用电量
                    $month_grab_garbage[$factory->code] = $grabGarbageObj->chartData($start_date, $end_date, $factory->code);  //垃圾入炉量
                    $month_weigh_bridge[$factory->code] = $weighBridgeObj->chartData($start_date, $end_date, $factory->code);  //垃圾入库量
                    $type_weigh_bridge[$factory->code] = $weighBridgeObj->chartType($the_day_after_start_date, $end_date, $factory->code);  //垃圾入库类别统计
                    $season_weigh_bridge[$factory->code] = $weighBridgeObj->season($year, $factory->code);  //垃圾入库季度统计
                }
            }

            //上网电量和厂用电量 各个电厂累计
            foreach ($month_electricity as $code => $factory_electricity) {
                foreach ($factory_electricity as $k1 => $itemlist) {
                    //不存在则赋初始值
                    if(!isset($final[$itemlist['en_name']])){
                        $final[$itemlist['en_name']] = array(
                            'en_name' => $itemlist['en_name'],
                            'cn_name' => $itemlist['cn_name'],
                            'messure' => $itemlist['messure'],
                            'datalist' => $datelist,
                            'no_hb' => false,
                            'hb' => $datelist
                        );
                    }

                    if($itemlist['datalist'] && count($itemlist['datalist']) > 0){
                        for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                            $date = date('Y-m-d', $i);
                            $final[$itemlist['en_name']]['datalist'][$date] = 0; //初始值
                            foreach ($itemlist['datalist'] as $k2 => $item) {
                                if($item->date == $date){
                                    //有当天数据
                                    $final[$itemlist['en_name']]['datalist'][$date] = (float)$item->val + $final[$itemlist['en_name']]['datalist'][$date];
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            //上网电量和厂用电量 各个电厂累计
            foreach ($season_electricity as $code => $factory_weigh_bridge) {
                if(!isset($final[$factory_weigh_bridge['en_name']])){
                    $final[$factory_weigh_bridge['en_name']] = array(
                        'en_name' => $factory_weigh_bridge['en_name'],
                        'cn_name' => $factory_weigh_bridge['cn_name'],
                        'messure' => $factory_weigh_bridge['messure'],
                        'datalist' => [],
                        'no_hb' => true
                    );
                }

                if($factory_weigh_bridge['datalist'] && count($factory_weigh_bridge['datalist']) > 0){
                    foreach ($factory_weigh_bridge['datalist'] as $season => $value) {
                        if(isset($final[$factory_weigh_bridge['en_name']]['datalist'][$season])){
                            $final[$factory_weigh_bridge['en_name']]['datalist'][$season] = (float)$value + $final[$factory_weigh_bridge['en_name']]['datalist'][$season];
                        }
                        else{
                            $final[$factory_weigh_bridge['en_name']]['datalist'][$season] = (float)$value;
                        }
                        $final[$factory_weigh_bridge['en_name']]['datalist'][$season] = (float)sprintf("%01.2f", $final[$factory_weigh_bridge['en_name']]['datalist'][$season]);
                    }
                }
            }

            //垃圾入炉量 各个电厂累计
            foreach ($month_grab_garbage as $code => $factory_grab_garbage) {
                //不存在则赋初始值
                if(!isset($final[$factory_grab_garbage['en_name']])){
                    $final[$factory_grab_garbage['en_name']] = array(
                        'en_name' => $factory_grab_garbage['en_name'],
                        'cn_name' => $factory_grab_garbage['cn_name'],
                        'messure' => $factory_grab_garbage['messure'],
                        'datalist' => $datelist,
                        'no_hb' => false,
                        'hb' => $datelist
                    );
                }

                if($factory_grab_garbage['datalist'] && count($factory_grab_garbage['datalist']) > 0){
                    for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                        $date = date('Y-m-d', $i);
                        foreach ($factory_grab_garbage['datalist'] as $k2 => $item) {
                            if($item->date == $date){
                                $final[$factory_grab_garbage['en_name']]['datalist'][$date] = (float)$item->val + $final[$factory_grab_garbage['en_name']]['datalist'][$date];
                                break;
                            }
                        }
                    }
                }
            }

            //垃圾入库量 各个电厂累计
            foreach ($month_weigh_bridge as $code => $factory_weigh_bridge) {
                //不存在则赋初始值
                if(!isset($final[$factory_weigh_bridge['en_name']])){
                    $final[$factory_weigh_bridge['en_name']] = array(
                        'en_name' => $factory_weigh_bridge['en_name'],
                        'cn_name' => $factory_weigh_bridge['cn_name'],
                        'messure' => $factory_weigh_bridge['messure'],
                        'datalist' => $datelist,
                        'no_hb' => false,
                        'hb' => $datelist
                    );
                }

                if($factory_weigh_bridge['datalist'] && count($factory_weigh_bridge['datalist']) > 0){
                    for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                        $date = date('Y-m-d', $i);
                        foreach ($factory_weigh_bridge['datalist'] as $k2 => $item) {
                            if($item->date == $date){
                                $final[$factory_weigh_bridge['en_name']]['datalist'][$date] = (float)$item->val + $final[$factory_weigh_bridge['en_name']]['datalist'][$date];
                                break;
                            }
                        }
                    }
                }
            }

            //垃圾入库类别 各个电厂累计
            foreach ($type_weigh_bridge as $code => $factory_weigh_bridge) {
                if(!isset($final[$factory_weigh_bridge['en_name']])){
                    $final[$factory_weigh_bridge['en_name']] = array(
                        'en_name' => $factory_weigh_bridge['en_name'],
                        'cn_name' => $factory_weigh_bridge['cn_name'],
                        'messure' => $factory_weigh_bridge['messure'],
                        'datalist' => [],
                        'no_hb' => true
                    );
                }

                if($factory_weigh_bridge['datalist'] && count($factory_weigh_bridge['datalist']) > 0){
                    foreach ($factory_weigh_bridge['datalist'] as $k2 => $item) {
                        if(isset($final[$factory_weigh_bridge['en_name']]['datalist'][$item['name']])){
                            $final[$factory_weigh_bridge['en_name']]['datalist'][$item['name']] = (float)$item['val'] + $final[$factory_weigh_bridge['en_name']]['datalist'][$item['name']];
                        }
                        else{
                            $final[$factory_weigh_bridge['en_name']]['datalist'][$item['name']] = (float)$item['val'];
                        }
                        $final[$factory_weigh_bridge['en_name']]['datalist'][$item['name']] = (float)sprintf("%01.2f", $final[$factory_weigh_bridge['en_name']]['datalist'][$item['name']]);
                    }
                }
            }

            //季度垃圾入库 各个电厂累计
            foreach ($season_weigh_bridge as $code => $factory_weigh_bridge) {
                if(!isset($final[$factory_weigh_bridge['en_name']])){
                    $final[$factory_weigh_bridge['en_name']] = array(
                        'en_name' => $factory_weigh_bridge['en_name'],
                        'cn_name' => $factory_weigh_bridge['cn_name'],
                        'messure' => $factory_weigh_bridge['messure'],
                        'datalist' => [],
                        'no_hb' => true
                    );
                }

                if($factory_weigh_bridge['datalist'] && count($factory_weigh_bridge['datalist']) > 0){
                    foreach ($factory_weigh_bridge['datalist'] as $season => $value) {
                        if(isset($final[$factory_weigh_bridge['en_name']]['datalist'][$season])){
                            $final[$factory_weigh_bridge['en_name']]['datalist'][$season] = (float)$value + $final[$factory_weigh_bridge['en_name']]['datalist'][$season];
                        }
                        else{
                            $final[$factory_weigh_bridge['en_name']]['datalist'][$season] = (float)$value;
                        }
                        $final[$factory_weigh_bridge['en_name']]['datalist'][$season] = (float)sprintf("%01.2f", $final[$factory_weigh_bridge['en_name']]['datalist'][$season]);
                    }
                }
            }
        }

        $datalist = [];
        foreach ($final as $k1 => $item) {
            if(!isset($item['no_hb']) || (isset($item['no_hb']) && !$item['no_hb'])){
                //非垃圾类别的去处第一条数据
                $i = 0 ;
                $lists = [];
                $hb = [];
                $preValue = 0;
                foreach ($item['datalist'] as $date => $value) {
                    if($i != 0){
                        $lists[$date] = (float)sprintf("%01.2f", $value);
                        $ratio = $preValue ? 100 * ($value - $preValue)/$preValue : 0;
                        $ratio = strpos($ratio, '.') !== false ? (float)sprintf("%01.2f", $ratio) : $ratio;
                        $hb[$date] = $ratio;
                    }

                    $preValue = $value;
                    $i++;
                }
                $item['datalist'] = $lists;
                $item['hb'] = $hb;
            }
            $datalist[] = $item;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $datalist);
    }

    /**
     * @OA\Get(
     *     path="/api/screen/boiler-temperature",
     *     tags={"大数据大屏screen"},
     *     operationId="screen-boiler-temperature",
     *     summary="炉温分布",
     *     description="使用说明：炉温分布",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="电厂ID",
     *         in="query",
     *         name="factory_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
     *         required=false,
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
    public function boilerTemperature(Request $request)
    {
        //获取接收的参数
        $factory_id = $request->input('factory_id');
        $start = $request->input('start');
        $end = $request->input('end');

        //初始化参数
        $final = [];
        $dcsStandardObj = new DcsStandardRepository();
        $begin_timestamp = $start ? strtotime($start) : time() - 24 * 60 * 60;
        $end_timestamp = $end ? strtotime($end) : time();
        $start_datetime = date('Y-m-d H:i:s', $begin_timestamp);
        $end_datetime = date('Y-m-d H:i:s', $end_timestamp);

        //获取电厂组织
        $factory = Orgnization::where('id', $factory_id)->first();
        if($factory){
            $datalist = $dcsStandardObj->countData($start_datetime, $end_datetime, $factory, $this->mongo_conn);
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $datalist);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, []);
        }
    }
}
