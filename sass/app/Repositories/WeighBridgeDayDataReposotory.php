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
            $datalist[$key]['val'] = (float)sprintf("%01.2f", $item['val']/1000);
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
}
