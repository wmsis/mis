<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Models\SIS\DailyData;
use App\Http\Models\SIS\EconomyRunData;
use App\Http\Models\SIS\EconomyDaily;

class EconomyDailyTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    public $tries = 3;

    /**
     * 搜集经济日报数据
     *
     * @return void
     */
     public function __construct($date=null)
     {
         $this->date = $date ? $date : date('Y-m-d');
     }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tagdaily = config('economydaily.tagdaily');
        $taglist = config('economydaily.taglist');
        $date = $this->date;
        $start = $date. ' 00:00:00';
        $end = $date. ' 23:59:59';

        //累计数据库中的值
        foreach ($tagdaily as $key1 => $items) {
            foreach ($items as $key2 => $item) {
                $en_name = $key1.'@'.$item['en_name'];
                if($item['en_name'] == 'run_status' || $item['en_name'] == 'entry_steam_flow' || $item['en_name'] == 'steam_flow'){
                    $day = (int)date('j', strtotime($date));  //月份中的第几天，没有前导零
                    if($day >= 2){
                        //累计当月的数值
                        $this->addUp($item['cn_name'], $en_name, 'database', $date);
                    }
                }
                else{
                    //查询每日所有的记录值，计算当日的平均值，最高最低值
                    $lists = EconomyRunData::where('time', '>=', $start)
                        ->where('time', '<=', $end)
                        ->where('en_name', $en_name)
                        ->get();

                    $total = 0;
                    $num = count($lists);
                    $min = $num > 0 ? $lists[0]->value : 0;
                    $max = $num > 0 ? $lists[0]->value : 0;
                    $avg = 0;

                    //取最大最小平均值
                    foreach ($lists as $key3 => $value) {
                        $total += $value->value;
                        if($value->value > $max){
                            $max = $value->value;
                        }
                        elseif($value->value < $min){
                            $min = $value->value;
                        }
                    }
                    $avg = $num ? $total/$num : 0;

                    $row = DailyData::where('date', $date)->where('en_name', $en_name)->first();
                    if (!$row) {
                        DailyData::create([
                            "cn_name" => $item['cn_name'],
                            "en_name" => $en_name,
                            "value" => $this->roundVal($avg),
                            "min" => $this->roundVal($min),
                            "max" => $this->roundVal($max),
                            "date" => $date
                        ]);
                    }
                    else{
                        $row->value = $this->roundVal($avg);
                        $row->min = $this->roundVal($min);
                        $row->max = $this->roundVal($max);
                        $row->save();
                    }
                }
            }
        }

        //累计手动输入的总值
        $k_arr = array(
            'no1_electric_energy',
            'no1_online_electric_energy',
            'no2_electric_energy',
            'no2_online_electric_energy',
            'factory_use_electricity',
            'out_buy_electricity',
            'leachate_station_use_electricity',
            'supply_first_factory_electricity',
            'out_buy_water',
            'boiler_use_water',
            'use_oil',
            'use_cement',
            'life_rubbish_entry',
            'rubbish_incineration',
            'waste_water_out_transport',
            'water_station_handle',
            'water_station_produce',
            'no1boiler_run_time',
            'no2boiler_run_time',
            'no3boiler_run_time',
            'no1turbine_run_time',
            'no2turbine_run_time'
        );

        foreach ($taglist as $key => $item) {
            if(in_array($item['en_name'], $k_arr)){
                $day = (int)date('j', strtotime($date));  //月份中的第几天，没有前导零
                if($day >= 2){
                    //累计当月的数值
                    $this->addUp($item['cn_name'], $item['en_name'], 'input', $date);
                }
            }
        }
    }

    //计算累计的值
    private function addUp($cn_name, $en_name, $type, $date){
        $startDate = date('Y-m', strtotime($date)).'-01';
        $endDate = date('Y-m-d', strtotime($date));

        if($type == 'database'){
            $rowSum = DailyData::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->where('en_name', $en_name)
                ->groupBy('en_name')
                ->sum('value');
        }
        else{
            $rowSum = EconomyDaily::where('time', '>=', $startDate)
                ->where('time', '<=', $endDate)
                ->where('en_name', $en_name)
                ->groupBy('en_name')
                ->sum('value');
        }

        $row = DailyData::where('date', $date)->where('en_name', $en_name.'@total')->first();
        if (!$row) {
            DailyData::create([
                "cn_name" => $cn_name.'累计',
                "en_name" => $en_name.'@total',
                "value" => $this->roundVal($rowSum),
                "min" => null,
                "max" => null,
                "date" => $date
            ]);
        }
        else{
            $row->value = $this->roundVal($rowSum);
            $row->cn_name = $cn_name.'累计';//中文名字覆盖
            $row->save();
        }
    }

    private function roundVal($val){
        if($val > 100){
            return round($val);
        }
        elseif($val > 1 && $val < 100){
            return round($val, 3);
        }
        else{
            return round($val, 4);
        }
    }
}
