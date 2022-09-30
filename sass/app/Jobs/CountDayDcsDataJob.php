<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SIS\DcsStandard;
use App\Models\Mongo\HistorianFormatData;

class CountDayDcsDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn; //租户连接
    protected $historian_day_data_table; //本地日累计数据表
    protected $historian_format_data_table; //本地保存的格式化后的数据集合
    public $tries = 3;

    /**
     * 累计每日DCS测点的值.
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->tenement_mongo_conn = $params && isset($params['tenement_mongo_conn']) ? $params['tenement_mongo_conn'] : '';
        $this->historian_format_data_table = $params && isset($params['historian_format_data_table']) ? $params['historian_format_data_table'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(strtotime($this->date . ' 00:00:00') < time()){
            if($this->date == date('Y-m-d')){
                //today
                $start = $this->date . ' 00:00:00';
                $end = date('Y-m-d H:i:s');//now
            }
            else{
                //the day before
                $start = $this->date . ' 00:00:00';
                $end = $this->date . ' 23:59:59';
            }

            $dcs_standard = (new DcsStandard())->setConnection($this->tenement_conn); //连接特定租户下面的标准DCS名称表
            $historian_format_data = (new HistorianFormatData())->setConnection($this->tenement_mongo_conn)->setTable($this->historian_format_data_table);//连接特定租户下面的格式化后的历史数据表
            $dcs_standard_list = $dcs_standard->get();
            foreach ($dcs_standard_list as $key => $item) {
                //累计某天的值
                $count_data = $historian_format_data->where('dcs_standard_id', $item->id)
                    ->where('datetime', '>', $start)
                    ->where('datetime', '<', $end)
                    ->sum('value');

                //保存累计值
            }
        }
    }
}
