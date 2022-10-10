<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ElectricityDayDataRepository;
use UtilService;

class DataAnalysisController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/data-analysis/total",
     *     tags={"数据分析data-analysis"},
     *     operationId="data-analysis-total",
     *     summary="首页上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标同比环比",
     *     description="使用说明：首页发电量 上网电量 厂用电量  垃圾入库量  垃圾入炉量等指标同比环比",
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
            $final['en_name'] = $item['yestoday']['en_name'];
            $final['cn_name'] = $item['yestoday']['cn_name'];
            $final['messure'] = $item['yestoday']['messure'];
            $final['yestoday_value'] = $item['yestoday']['messure']
        }

        $final = array(
            'yestoday' => $yestoday_electricity,
            'day_before_yestoday' => $day_before_yestoday_electricity,
            'year_before' => $year_before_electricity
        );

        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $key_values);
    }

    /**
     * @OA\Get(
     *     path="/api/data-analysis/chart",
     *     tags={"数据分析data-analysis"},
     *     operationId="data-analysis-chart",
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
    }
}
