<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SIS\ElectricityDayData;
use App\Models\SIS\ElectricityMap;
use App\Models\SIS\PowerMap;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\PowerDayData;
use Illuminate\Support\Facades\DB;

class CountDayPowerDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn; //租户连接
    protected $electricity_day_data_table; //本地日累计数据表
    protected $power_day_data_table;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->electricity_day_data_table = $params && isset($params['electricity_day_data_table']) ? $params['electricity_day_data_table'] : '';
        $this->power_day_data_table = $params && isset($params['power_day_data_table']) ? $params['power_day_data_table'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(strtotime($this->date . ' 00:00:00') < time()){
            //获取厂用电量 上网电量
            $lists = DB::connection($this->tenement_conn)->table('dcs_standard')
                ->join('power_map', 'power_map.dcs_standard_id', '=', 'dcs_standard.id')
                ->select('power_map.*', 'dcs_standard.cn_name', 'dcs_standard.en_name')
                ->where('dcs_standard.type', 'electricity')
                ->get();

            $final = [];
            foreach ($lists as $k1 => $item) {
                $id_arr = explode(',', $item->electricity_map_ids);
                $func = $item->func;
                $electricityObj = (new ElectricityDayData())->setConnection($this->tenement_conn)->setTable($this->electricity_day_data_table);

                //获取日期范围内用电量 上网电量具体数据
                $datalist = $electricityObj->where('date', '>=', $this->date)
                    ->where('date', '<=', $this->date)
                    ->whereIn('electricity_map_id', $id_arr)
                    ->selectRaw('SUM(value) as val, electricity_map_id')
                    ->groupBy('electricity_map_id')
                    ->get();

                $electricityMapObj = (new ElectricityMap())->setConnection($this->tenement_conn);
                foreach ($datalist as $k2 => $data) {
                    $map = $electricityMapObj->find($data->electricity_map_id);
                    $datalist[$k2]['cn_name'] = $map && $map->cn_name ? $map->cn_name : '';
                }

                //地址键值对
                $key_values = [];
                foreach ($datalist as $k3 => $data) {
                    $key_values[$data->cn_name] = $data->val;
                }

                $val = 0;
                if($func && count($key_values) > 0){
                    //计算函数的值
                    foreach ($key_values as $key => $value) {
                        $func = str_replace('[' . $key . ']', $value, $func);
                    }

                    $val = eval("return $func;");
                }

                if(strpos($val, '.') !== false){
                    $val = (float)number_format($val, 2);
                }

                //保存发电量  上网电量每日累计数据
                $powerDayObj = (new PowerDayData())->setConnection($this->tenement_conn)->setTable($this->power_day_data_table);
                $row = $powerDayObj->where('date', $this->date)->where('power_map_id', $item->id)->first();
                if($row && $row->id){
                    $row->value = $val;
                    $row->save();
                }
                else{
                    $powerDayObj->create([
                        'power_map_id' => $item->id,
                        'date' => $this->date,
                        'value' => $val
                    ]);
                }
            }
        }
    }
}
