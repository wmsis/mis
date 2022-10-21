<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ElectricityDayDataRepository;
use UtilService;
use EconomyDailyService;

class DataAnalysisController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/data-analysis/total",
     *     tags={"数据分析data-analysis"},
     *     operationId="data-analysis-total",
     *     summary="上网电量 厂用电量等指标同比环比",
     *     description="使用说明：上网电量 厂用电量等指标同比环比",
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
        $key_values = array();
        //2、昨日累计值
        $start = date('Y-m-d', time() - 24 * 60 * 60);
        $end = $start;
        $yestoday_electricity = $electricityObj->countData($start, $end, $this->orgnization->code);  //上网电量 厂用电量
        foreach ($yestoday_electricity as $key => $item) {
            $key_values[$item['en_name']]['yestoday'] = $item;
        }

        //2、前日累计值 环比日
        $start = date('Y-m-d', time() - 48 * 60 * 60);
        $end = $start;
        $day_before_yestoday_electricity = $electricityObj->countData($start, $end, $this->orgnization->code);  //上网电量 厂用电量
        foreach ($day_before_yestoday_electricity as $key => $item) {
            $key_values[$item['en_name']]['day_before_yestoday'] = $item;
        }

        //2、上年同日累计值 同比日
        $start = date('Y-m-d', time() - 365 * 24 * 60 * 60);
        $end = $start;
        $year_before_electricity = $electricityObj->countData($start, $end, $this->orgnization->code);  //上网电量 厂用电量
        foreach ($year_before_electricity as $key => $item) {
            $key_values[$item['en_name']]['year_before'] = $item;
        }

        $final = [];
        foreach ($key_values as $key => $item) {
            $temp = array();
            $temp['en_name'] = $item['yestoday']['en_name'];
            $temp['cn_name'] = $item['yestoday']['cn_name'];
            $temp['messure'] = $item['yestoday']['messure'];
            $temp['yestoday_value'] = (float)$item['yestoday']['value'];  //当前值
            $temp['day_before_yestoday_value'] = (float)$item['day_before_yestoday']['value'];//上期值
            $temp['year_before_value'] = (float)$item['year_before']['value'];//同期值
            $temp['year_over_year'] = $item['yestoday']['value'] ? number_format(($temp['yestoday_value'] - $temp['year_before_value']) * 100 / $temp['yestoday_value'], 2) . '%' : '0%'; //同比
            $temp['month_over_month'] = $item['yestoday']['value'] ? number_format(($temp['yestoday_value'] - $temp['day_before_yestoday_value']) * 100 / $temp['yestoday_value'], 2) . '%' : '0%';//环比
            $final[] = $temp;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/data-analysis/chart",
     *     tags={"数据分析data-analysis"},
     *     operationId="data-analysis-chart",
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
     *         description="开始时间",
     *         in="query",
     *         name="start",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="结束时间",
     *         in="query",
     *         name="end",
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
        $start = $request->input('start');
        $end = $request->input('end');
        $final = [];
        $electricityObj = new ElectricityDayDataRepository();

        //曲线图
        $begin_timestamp = $start ? strtotime($start) : time() - 30 * 24 * 60 * 60;
        $end_timestamp = $end ? strtotime($end) : time() - 24 * 60 * 60;
        $start_date = date('Y-m-d', $begin_timestamp);
        $end_date = date('Y-m-d', $end_timestamp);
        $month_electricity = $electricityObj->chartData($start_date, $end_date, $this->orgnization->code);  //垃圾入库量

        //赋值
        //循环发电量和上网电量  month_electricity包含发电量和上网电量
        foreach ($month_electricity as $k1 => $itemlist) {
            //遍历其中一个
            $temp = array(
                'en_name' => $itemlist['en_name'],
                'cn_name' => $itemlist['cn_name'],
                'messure' => $itemlist['messure'],
                'datalist' => [],
            );

            if($itemlist['datalist'] && count($itemlist['datalist']) > 0){
                for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                    $date = date('Y-m-d', $i);
                    $temp['datalist'][$date] = 0; //初始值
                    foreach ($itemlist['datalist'] as $k2 => $item) {
                        if($item->date == $date){
                            //有当天数据
                            $temp['datalist'][$date] = (float)$item->val;
                            break;
                        }
                    }
                }
            }
            else{
                for($i=$begin_timestamp; $i<=$end_timestamp; $i=$i+24*60*60){
                    $date = date('Y-m-d', $i);
                    $temp['datalist'][$date] = 0; //初始值
                }
            }
            $final[] = $temp;
        }

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }

    /**
     * @OA\Get(
     *     path="/api/data-analysis/economy-daily",
     *     tags={"数据分析data-analysis"},
     *     operationId="data-analysis-economy-daily",
     *     summary="经济日报表",
     *     description="使用说明：经济日报表",
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
     *         description="时间",
     *         in="query",
     *         name="date",
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
    public function economyDaily(Request $request)
    {
        $date = $request->input('date');
        if(!$date){
            return UtilService::format_data(self::AJAX_FAIL, '日期不能为空', '');
        }
        elseif(strtotime($date . ' 00:00:00') > time()){
            return UtilService::format_data(self::AJAX_FAIL, '日期不能大于今天', '');
        }

        $final = EconomyDailyService::daydata($date);
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $final);
    }
}
