<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\SIS\ElectricityDayData;
use App\Models\SIS\PowerMap;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\ElectricityMap;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Log;

/**
 * Class ElectricityDayDataRepository.
 */
class ElectricityDayDataRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return ElectricityDayData::class;
    }

    public function countData($start, $end, $factory, $tenement_conn=null)
    {
        $final = [];
        $standard_lists = DcsStandard::where('type', 'electricity')->where('is_show', 1)->orderBy('sort', 'ASC')->get();
        foreach ($standard_lists as $key => $item) {
            //获取厂用电量 上网电量
            if(!$tenement_conn){
                $sum_value = DB::table('power_map')
                    ->join('power_day_data_' . $factory, 'power_day_data_' . $factory . '.power_map_id', '=', 'power_map.id')
                    ->where('power_map.dcs_standard_id', $item->id)
                    ->where('power_day_data_' . $factory . '.date', '>=', $start)
                    ->where('power_day_data_' . $factory . '.date', '<=', $end)
                    ->sum('power_day_data_' . $factory . '.value');
            }
            else{
                $sum_value = DB::connection($tenement_conn)->table('power_map')
                    ->join('power_day_data_' . $factory, 'power_day_data_' . $factory . '.power_map_id', '=', 'power_map.id')
                    ->where('power_map.dcs_standard_id', $item->id)
                    ->where('power_day_data_' . $factory . '.date', '>=', $start)
                    ->where('power_day_data_' . $factory . '.date', '<=', $end)
                    ->sum('power_day_data_' . $factory . '.value');
            }

            $final[] = array(
                'cn_name' => $item->cn_name,
                'en_name' => $item->en_name,
                'value' => (float)sprintf("%01.2f", (float)$sum_value/10000),
                'messure' => '万度',
            );
        }

        return $final;
    }

    public function chartData($start, $end, $factory)
    {
        //获取厂用电量 上网电量
        $final = [];
        $standard_lists = DcsStandard::where('type', 'electricity')->where('is_show', 1)->orderBy('sort', 'ASC')->get();
        foreach ($standard_lists as $key => $item) {
            $temp = [];
            $datalist = DB::table('power_map')
                ->join('power_day_data_' . $factory, 'power_day_data_' . $factory . '.power_map_id', '=', 'power_map.id')
                ->where('power_map.dcs_standard_id', $item->id)
                ->where('power_day_data_' . $factory . '.date', '>=', $start)
                ->where('power_day_data_' . $factory . '.date', '<=', $end)
                ->selectRaw('SUM(power_day_data_' . $factory . '.value) as val, power_day_data_' . $factory . '.date')
                ->groupBy('power_day_data_' . $factory . '.date')
                ->get();

            foreach ($datalist as $key => $value) {
                $datalist[$key]->val = (float)sprintf("%01.2f", (float)($value->val/10000));
            }

            $temp['datalist'] = $datalist;
            $temp['en_name'] = $item->en_name;
            $temp['cn_name'] = $item->cn_name;
            $temp['messure'] = '万度';
            $final[] = $temp;
        }

        return $final;
    }
}
