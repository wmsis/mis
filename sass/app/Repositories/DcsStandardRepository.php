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
        return HistorianFormatData::class;
    }

    public function rangeData($start, $end, $factory, $tenement_mongo_conn=null)
    {
        $final = [];
        $range = array(
            0, 800, 850, 900, 950, 1000, 1050, 1100, 1200, 1300
        );
        $cfg = config('standard.boiler.GL1_LTSBWD_L');
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

        return array_values($final);
    }
}
