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

    public function countData($start, $end, $factory)
    {
        $final = [];
        $table = 'grab_garbage_day_data_' . $factory;
        $grabGarbageObj = (new GrabGarbageDayData())->setTable($table);

        //获取日期范围内用电量 上网电量具体数据
        $sum_value = $grabGarbageObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('value');

        $final[] = array(
            'cn_name' => '垃圾入炉量',
            'en_name' => 'ljrll',
            'value' => $sum_value,
            'messure' => 'KG'
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
            ->selectRaw('MAX(value) as val, date')
            ->groupBy('date')
            ->get();

        $final['datalist'] = $datalist;
        $final['en_name'] = 'ljrll';
        $final['cn_name'] = '垃圾入炉量';
        $final['messure'] = 'KG';

        return $final;
    }
}
