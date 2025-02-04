<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\SIS\GrabGarbageDayData;

/**
 * Class GrabGarbageDayDataReposotory.
 */
class GrabGarbageDayDataReposotory extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return GrabGarbageDayData::class;
    }

    public function countData($start, $end, $factory, $tenement_conn=null)
    {
        $final = [];
        $table = 'grab_garbage_day_data_' . $factory;

        //获取日期范围内用电量 上网电量具体数据
        if(!$tenement_conn){
            $grabGarbageObj = (new GrabGarbageDayData())->setTable($table);
        }
        else{
            $grabGarbageObj = (new GrabGarbageDayData())->setConnection($tenement_conn)->setTable($table);
        }
        $sum_value = $grabGarbageObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('value');

        $final[] = array(
            'cn_name' => config('standard.not_dcs.ljrll.cn_name'),
            'en_name' => config('standard.not_dcs.ljrll.en_name'),
            'value' => (float)($sum_value/1000),
            'messure' => '吨'
        );

        return $final;
    }

    public function chartData($start, $end, $factory)
    {
        $final = [];
        $table = 'grab_garbage_day_data_' . $factory;
        $grabGarbageObj = (new GrabGarbageDayData())->setTable($table);

        //获取日期范围内用电量 上网电量具体数据
        $datalist = $grabGarbageObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->selectRaw('SUM(value) as val, date')
            ->groupBy('date')
            ->get();

        foreach ($datalist as $key => $item) {
            $datalist[$key]['val'] = (float)sprintf("%01.1f", (float)($item->val/1000));
        }
        $final['datalist'] = $datalist;
        $final['en_name'] = config('standard.not_dcs.ljrll.en_name');
        $final['cn_name'] = config('standard.not_dcs.ljrll.cn_name');
        $final['messure'] = '吨';

        return $final;
    }
}
