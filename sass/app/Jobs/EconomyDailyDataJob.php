<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SIS\DcsStandard;
use App\Models\Mongo\HistorianData;
use App\Models\Mongo\HistorianFormatData;
use App\Models\Mongo\EconomyDailyData;
use Log;

class EconomyDailyDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn; //租户连接
    protected $tenement_mongo_conn; //租户连接
    protected $factory;

    /**
     * 收集日报数据，以天为单位
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->tenement_mongo_conn = $params && isset($params['tenement_mongo_conn']) ? $params['tenement_mongo_conn'] : '';
        $this->factory = $params && isset($params['factory']) ? $params['factory'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(strtotime($this->date . ' 00:00:00') < time()){
            $start = $this->date . ' 00:00:00';
            $end = $this->date . ' 23:59:59';
            $name_list = [];
            $lists = config('standard.daily');
            foreach ($lists as $k1 => $item) {
                $name_list[] = $item['en_name'];
            }
            $dcs_standard_obj = (new DcsStandard())->setConnection($this->tenement_conn);
            $dcs_standards = $dcs_standard_obj->whereIn('en_name', $name_list)->get();

            $historian_format_table = 'historian_format_data_' . $this->factory->code;
            $economy_daily_table = 'economy_daily_data_' . $this->factory->code;
            $hitorian_format_obj = (new HistorianFormatData())->setConnection($this->tenement_mongo_conn)->setTable($historian_format_table);
            $economy_daily_obj = (new EconomyDailyData())->setConnection($this->tenement_mongo_conn)->setTable($economy_daily_table);
            foreach ($dcs_standards as $k1 => $item) {
                $max_value = $hitorian_format_obj->where('dcs_standard_id', $item->id)
                    ->where('datetime', '>=', $start)
                    ->where('datetime', '<=', $end)
                    ->max('value');

                $min_value = $hitorian_format_obj->where('dcs_standard_id', $item->id)
                    ->where('datetime', '>=', $start)
                    ->where('datetime', '<=', $end)
                    ->min('value');

                $avg_value = $hitorian_format_obj->where('dcs_standard_id', $item->id)
                    ->where('datetime', '>=', $start)
                    ->where('datetime', '<=', $end)
                    ->avg('value');

                $row = $economy_daily_obj->findByIdAndDate($item->id, $this->date);
                if($row && $row->_id){
                    $row->avg_value = $avg_value;
                    $row->min_value = $min_value;
                    $row->max_value = $max_value;
                    $row->save();
                }
                else{
                    $economy_daily_obj->create([
                        'avg_value' => $avg_value,
                        'min_value' => $min_value,
                        'max_value' => $max_value
                    ]);
                }
            }
        }
    }
}
