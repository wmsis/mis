<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SIS\GrabGarbage;
use App\Models\SIS\GrabGarbageDayData;
use Log;

class CountDayGrabGarbageDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn; //租户连接
    protected $grab_garbage_day_data_table; //本地日累计数据表
    protected $grab_garbage_table; //本地保存的数据集合
    public $tries = 3;

    /**
     * 累计每日垃圾入炉量的值.
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->grab_garbage_day_data_table = $params && isset($params['grab_garbage_day_data_table']) ? $params['grab_garbage_day_data_table'] : '';
        $this->grab_garbage_table = $params && isset($params['grab_garbage_table']) ? $params['grab_garbage_table'] : '';
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

            $grab_garbage = (new GrabGarbage())->setConnection($this->tenement_conn)->setTable($this->grab_garbage_table); //连接特定租户下面的标准DCS名称表
            $grab_garbage_day_data = (new GrabGarbageDayData())->setConnection($this->tenement_conn)->setTable($this->grab_garbage_day_data_table);//连接特定租户下面的格式化后的历史数据表
            $grab_garbage_sum = $grab_garbage->where('created_at', '>', $start)
                ->where('created_at', '<', $end)
                ->selectRaw('SUM(hev) as total, liao')
                ->groupBy('liao')
                ->get();

            //保存累计值
            foreach ($grab_garbage_sum as $key => $item) {
                $row = $grab_garbage_day_data->where('date', $this->date)->where('liao', $item['liao'])->first();
                if($row && $row->id){
                    $row->date = $this->date;
                    $row->value = $item['total'];
                    $row->liao = $item['liao'];
                    $row->save();
                }
                else{
                    $grab_garbage_day_data->create([
                        'date' => $this->date,
                        'value' => $item['total'],
                        'liao' => $item['liao']
                    ]);
                }
            }
        }
    }
}
