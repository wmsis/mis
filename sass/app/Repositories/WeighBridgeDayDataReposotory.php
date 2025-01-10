<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\SIS\WeighBridgeDayData;
use Illuminate\Support\Facades\DB;

/**
 * Class WeighBridgeDayDataReposotory.
 */
class WeighBridgeDayDataReposotory extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return WeighBridgeDayData::class;
    }

    public function countData($start, $end, $factory, $tenement_conn=null)
    {
        $final = [];
        $table = 'weighbridge_day_data_' . $factory;

        //获取日期范围内具体数据
        if(!$tenement_conn){
            $weighBridgeObj = (new WeighBridgeDayData())->setTable($table);
        }
        else{
            $weighBridgeObj = (new WeighBridgeDayData())->setConnection($tenement_conn)->setTable($table);
        }
        $sum_value = $weighBridgeObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('value');

        //垃圾入库量
        $final[] = array(
            'cn_name' => config('standard.not_dcs.ljrkl.cn_name'),
            'en_name' => config('standard.not_dcs.ljrkl.en_name'),
            'value' => (float)($sum_value/1000),
            'messure' => '吨'
        );

        return $final;
    }

    public function chartData($start, $end, $factory)
    {
        $final = [];
        $table = 'weighbridge_day_data_' . $factory;
        $weighBridgeObj = (new WeighBridgeDayData())->setTable($table);

        //获取日期范围内具体数据
        $datalist = $weighBridgeObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->selectRaw('SUM(value) as val, date')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        foreach ($datalist as $key => $item) {
            $datalist[$key]['val'] = (float)sprintf("%01.1f", $item['val']/1000);
        }
        $final['datalist'] = $datalist;
        $final['en_name'] = config('standard.not_dcs.ljrkl.en_name');
        $final['cn_name'] = config('standard.not_dcs.ljrkl.cn_name');
        $final['messure'] = '吨';

        return $final;
    }

    public function chartType($start, $end, $factory)
    {
        $final = [];
        $table = 'weighbridge_day_data_' . $factory;
        $weighBridgeObj = (new WeighBridgeDayData())->setTable($table);

        //获取日期范围内具体数据
        $datalist = $weighBridgeObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->selectRaw('SUM(value) as val, weighbridge_cate_small_id')
            ->groupBy('weighbridge_cate_small_id')
            ->get()->toArray();

        foreach ($datalist as $key => $item) {
            $datalist[$key]['val'] = $item['val']/1000;
            $cate_big = DB::table('weighbridge_cate_small')
                ->join('weighbridge_cate_big', 'weighbridge_cate_big.id', '=', 'weighbridge_cate_small.weighbridge_cate_big_id')
                ->select(['weighbridge_cate_big.id', 'weighbridge_cate_big.name', 'weighbridge_cate_small.name as small_name'])
                ->where('weighbridge_cate_small.id', $item['weighbridge_cate_small_id'])
                ->first();
            $datalist[$key]['name'] = $cate_big ? $cate_big->name : '';
            $datalist[$key]['id'] = $cate_big ? $cate_big->id : '';
        }
        $final['datalist'] = $datalist;
        $final['en_name'] = config('standard.not_dcs.ljrk_type.en_name');
        $final['cn_name'] = config('standard.not_dcs.ljrk_type.cn_name');
        $final['messure'] = '吨';

        return $final;
    }

    //季度数据
    public function season($year=null, $factory){
        $final = [];
        $final['datalist'] = [];
        $table = 'weighbridge_day_data_' . $factory;
        $weighBridgeObj = (new WeighBridgeDayData())->setTable($table);
        $timestamp = strtotime($year . '-01-01');
        $season = 1;
        while($timestamp <= strtotime($year . '-12-01')){
            $season_start = date('Y-m', $timestamp) . '-01';
            $three_month_later = $timestamp + 3*28*24*60*60; //3*28天之后，肯定是这个季度最后一个月
            $season_end = date('Y-m-t', $three_month_later); //第三个月最后一天

            //获取日期范围内具体数据
            $season_value = $weighBridgeObj->where('date', '>=', $season_start)
                ->where('date', '<=', $season_end)
                ->sum('value');

            $final['datalist'][$season] = (float)sprintf("%01.1f", $season_value/10000000);

            //下个季度的某一天
            $next_season_one_day = $timestamp + 3*32*24*60*60; //3*32天之后，肯定是下个季度第一个月
            $next_start = date('Y-m', $next_season_one_day);   //为下个季度第一个月的数据作为计算数据
            $timestamp = strtotime($next_start . '-01');       //下一个月
            $season++;
        }
        $final['en_name'] = config('standard.not_dcs.ljrk_season.en_name');
        $final['cn_name'] = config('standard.not_dcs.ljrk_season.cn_name');
        $final['messure'] = '万吨';

        return $final;
    }
}
