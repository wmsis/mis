<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\SIS\WeighBridgeDayData;

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

    public function countData($start, $end, $factory)
    {
        $final = [];
        $table = 'weighbridge_day_data_' . $factory;
        $weighBridgeObj = (new WeighBridgeDayData())->setTable($table);

        //获取日期范围内用电量 上网电量具体数据
        $sum_value = $weighBridgeObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->sum('value');

        $final[] = array(
            'cn_name' => '垃圾入库量',
            'en_name' => 'ljrkl',
            'value' => $sum_value,
            'messure' => 'KG'
        );

        return $final;
    }

    public function chartData($start, $end, $factory)
    {
        $final = [];
        $table = 'weighbridge_day_data_' . $factory;
        $weighBridgeObj = (new WeighBridgeDayData())->setTable($table);

        //获取日期范围内用电量 上网电量具体数据
        $datalist = $weighBridgeObj->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->selectRaw('MAX(value) as val, date')
            ->groupBy('date')
            ->get();

        $final['datalist'] = $datalist;
        $final['en_name'] = 'ljrkl';
        $final['cn_name'] = '垃圾入库量';
        $final['messure'] = 'KG';

        return $final;
    }
}
