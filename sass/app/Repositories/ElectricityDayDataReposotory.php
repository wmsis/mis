<?php

namespace App\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\SIS\ElectricityDayData;
use App\Models\SIS\PowerMap;
use App\Models\SIS\DcsStandard;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

/**
 * Class ElectricityDayDataReposotory.
 */
class ElectricityDayDataReposotory extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return ElectricityDayData::class;
    }

    public function countData($start, $end)
    {
        $lists = DB::table('dcs_standard')
            ->join('power_map', 'dcs_standard.dcs_group_id', '=', 'power_map.id')
            ->select('dcs_standard.*', 'power_map.name AS group_name');
        //$this->where('')->get();
    }
}
