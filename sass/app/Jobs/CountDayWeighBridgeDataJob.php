<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SIS\WeighBridgeFormat;
use App\Models\SIS\WeighBridgeDayData;

class CountDayWeighBridgeDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn; //租户连接
    protected $weighbridge_day_data_table; //本地日累计数据表
    protected $weighbridge_format_data_table; //本地保存的数据集合
    public $tries = 3;

    /**
     * 累计每日垃圾入库量的值
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->weighbridge_day_data_table = $params && isset($params['weighbridge_day_data_table']) ? $params['weighbridge_day_data_table'] : '';
        $this->weighbridge_format_data_table = $params && isset($params['weighbridge_format_data_table']) ? $params['weighbridge_format_data_table'] : '';
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

            $weigh_bridge_format = (new WeighBridgeFormat())->setConnection($this->tenement_conn)->setTable($this->weighbridge_format_data_table); //连接特定租户下面的标准DCS名称表
            $weigh_bridge_day_data = (new WeighBridgeDayData())->setConnection($this->tenement_conn)->setTable($this->weighbridge_day_data_table);//连接特定租户下面的格式化后的历史数据表
            $weigh_bridge_sum = $weigh_bridge_format->where('taredatetime', '>=', $start)
                ->where('taredatetime', '<=', $end)
                ->selectRaw('SUM(net) as total, weighbridge_cate_small_id')
                ->groupBy('weighbridge_cate_small_id')
                ->get();

            //保存累计值
            foreach ($weigh_bridge_sum as $key => $item) {
                $row = $weigh_bridge_day_data->where('date', $this->date)->where('weighbridge_cate_small_id', $item['weighbridge_cate_small_id'])->first();
                if($row && $row->id){
                    $row->weighbridge_cate_small_id = $item['weighbridge_cate_small_id'];
                    $row->date = $this->date;
                    $row->value = $item['total'];
                    $row->save();
                }
                else{
                    $weigh_bridge_day_data->create([
                        'weighbridge_cate_small_id' => $item['weighbridge_cate_small_id'],
                        'date' => $this->date,
                        'value' => $item['total']
                    ]);
                }
            }
        }
    }
}
