<?php

namespace App\Repositories;

use App\Models\SIS\DcsStandard;
use App\Models\Mongo\HistorianFormatData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Log;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

/**
 * Class DcsStandardRepository.
 */
class DcsStandardRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return ElectricityDayData::class;
    }

    public function countData($start, $end, $factory, $tenement_mongo_conn=null)
    {
        $final = [];
        $range = array(
            0, 800, 850, 860, 870, 880, 890,
            900, 910, 920, 930, 940, 950, 960, 970, 980, 990,
            1000, 1110, 1120, 1130, 1140, 1150, 1160, 1170, 1180, 1190,
            1200, 1250, 1300, 1400
        );
        $cfg = config('standard.boiler.gl1_ltsbwd');
        $standard = DcsStandard::where('type', 'dcs')->where('en_name', $cfg['en_name'])->first();
        $table = 'historian_format_data_' . $factory['code'];
        $historian_format_obj = (new HistorianFormatData())->setConnection($tenement_mongo_conn)->setTable($table);

        for($i=1; $i<count($range)-1; $i++){
            $num = $historian_format_obj->where('dcs_standard_id', $standard['id'])
                ->where('datetime', '>=', $start)
                ->where('datetime', '<=', $end)
                ->where('value', '>=', $range[$i-1])
                ->where('value', '<', $range[$i])
                ->count();
            $key = $range[$i-1] . 'to' . $range[$i];
            $final[$key] = array(
                'value' => $num,
                'name' => $range[$i-1] . '~' . $range[$i] . '℃'
            );
        }

        return $final;
    }
}
