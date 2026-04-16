<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\QueryException;
use App\Models\SIS\Electricity;
use App\Models\SIS\NariData;
use ErrorException;
use Illuminate\Support\Facades\Log;

class NariDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tenement_conn; //租户连接
    protected $local_data_table; //本地保存的MongoDB原始数据集合
    protected $nari_data_table; //南瑞数据库
    protected $map; //本地保存的格式化后的数据集合
    protected $cfgdb;//数据库配置信息
    public $tries = 3;
    protected $info_list = [];

    /**
     * 
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->local_data_table = $params && isset($params['local_data_table']) ? $params['local_data_table'] : '';
        $this->nari_data_table = $params && isset($params['nari_data_table']) ? $params['nari_data_table'] : '';
        $this->map = $params && isset($params['map']) ? $params['map'] : '';
        $this->cfgdb = $params && isset($params['cfgdb']) ? $params['cfgdb'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $tags = $this->map;
        $cn_names = [];
        //配置关系
        foreach ($tags as $key => $item) {
            $cn_names[] = array(
                'name'=> $item['cn_name'],
                'electricity_map_id'=> $item['id'],
                'factor'=> $item['func'],
                'rate'=> $item['rate'] ? $item['rate'] : 1
            );
        }

        //获取南瑞数据
        $nari = (new NariData())->setConnection($this->tenement_conn)->setTable($this->nari_data_table);
        $datalist = $nari->findNotComputer(); 
        $ids = array();
        foreach($datalist as $item){
            $ids[] = $item->id;
            $this->info_list[$item->address] = [
                'addr'=> $item->address,
                'value'=> $item->value,
                'quality'=> $item->quality
            ];
        }

        ksort($this->info_list);
        $sorted_values = $this->info_list;
        $params = array();
        if(count($sorted_values) == count($tags)){
            $i = 0;
            foreach ($sorted_values as $key => $item) {
                $params[] = array(
                    "electricity_map_id" => $cn_names[$i]['electricity_map_id'],
                    "value" => $item['value'],
                    "actual_value" => $item['value'] * $cn_names[$i]['factor'] * $cn_names[$i]['rate']
                );
                $i++;
            }

            try {
                $nari->updateByIds(["flag" => 1], $ids);
                $electricity = (new Electricity())->setConnection($this->tenement_conn)->setTable($this->local_data_table);
                foreach ($params as $key => $value) {
                    $params[$key]['created_at'] = date('Y-m-d H:i:s');
                    $params[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
                $electricity->insertMany($params);
                //Log::info('=========操作成功=========');
            } catch (QueryException $e) {
                Log::info('获取南瑞电表数据异常');
                Log::info($e->getMessage());
            }
        }
        else{
            //Log::info('数据匹配不上');
        }
    }
}