<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Factory\Historian;   //电厂数据模型
use App\Models\Mongo\HistorianData; //本地数据模型
use App\Models\SIS\HistorianTag;    //本地数据标签模型
use Log;
use Config;
use HistorianService;

class HistorianDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $datetime;
    protected $tenement_conn;
    protected $tenement_mongo_conn;
    protected $remote_conn;
    protected $local_tag_table;
    protected $local_data_table;
    protected $db_type;
    protected $orgnization;
    public $tries = 3;

    /**
    * @param datetime 获取数据的日期时间
    * @param connection 获取数据的远程数据库连接
    * @param table 存储数据的本地数据库表
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->datetime = $params && isset($params['datetime']) ? $params['datetime'] : '';
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->tenement_mongo_conn = $params && isset($params['tenement_mongo_conn']) ? $params['tenement_mongo_conn'] : '';
        $this->remote_conn = $params && isset($params['remote_conn']) ? $params['remote_conn'] : '';
        $this->local_tag_table = $params && isset($params['local_tag_table']) ? $params['local_tag_table'] : '';
        $this->local_data_table = $params && isset($params['local_data_table']) ? $params['local_data_table'] : '';
        $this->db_type = $params && isset($params['db_type']) ? $params['db_type'] : '';
        $this->orgnization = $params && isset($params['orgnization']) ? $params['orgnization'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->db_type == 'historiandb'){
            $this->historiandb_data(); //读取historian7.0以上数据库的数据
        }
        else{
            $this->mongodb_data(); //若数据库historian为7.0以下，则从opcserver读取数据，OPC读取后转存到电厂本地MongoDB数据库
        }
    }

    private function historiandb_data(){
        try{
            $obj_hitorian_factory = (new HistorianTag())->setConnection($this->remote_conn)->setTable($this->local_tag_table);  //连接电厂数据库
            $obj_hitorian_local = (new HistorianData())->setConnection($this->tenement_mongo_conn)->setTable($this->local_data_table); //连接特定租户下面的本地数据库表
        }
        catch(Exception $ex){
            Log::info('连接电厂历史数据库异常');
            Log::info(var_export($ex, true));
        }

        $start = date('Y-m-d H:i', strtotime($this->datetime) - 60) . ':00';
        $start = gmdate("Y-m-d\TH:i:s\Z", strtotime($start)); //国际时间
        $end = date('Y-m-d H:i', strtotime($this->datetime)) . ':00';
        $end = gmdate("Y-m-d\TH:i:s\Z", strtotime($end)); //国际时间
        $obj_hitorian_factory->chunk(20, function ($tagslist) use ($obj_hitorian_local, $start, $end) {
            $params = [];
            $tagsNameList = [];
            foreach ($tagslist as $key => $tag) {
                $tagsNameList[] = $tag->tag_name;
            }
            $tagsNameString = implode(';', $tagsNameList);
            $count = 1;
            $samplingMode = 2;
            $calculationMode = 1;
            $intervalMS = null;
            $res = HistorianService::SampledData($this->orgnization, $tagsNameString, $start, $end, $count, $samplingMode, $calculationMode, $intervalMS);
            if($res && $res['code'] === 0 && $res['data']['ErrorCode'] === 0){
                $datalist = $res['data']['Data'];
                foreach ($datalist as $key => $item) {
                    $timestamp = '';
                    $value = '';
                    if(isset($item['Samples']) && $item['Samples'] && count($item['Samples']) > 0) {
                        $timestamp = $item['Samples'][0]['TimeStamp'];
                        $value = $item['Samples'][0]['Value'];
                    }
                    $local_row = $obj_hitorian_local->findRowByTagAndTime($item['TagName'], $timestamp);
                    if(!$local_row){
                        //本地不存在则插入
                        $params[] = array(
                            'tag_name' => $item['TagName'],
                            'value'=> $value,
                            'datetime'=> date('Y-m-d H:i:s', strtotime($timestamp)),
                            'created_at' => date('Y-m-d H:i:s', strtotime($timestamp)),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                }
            }

            if($params && count($params) > 0){
                $res = $obj_hitorian_local->insertMany($params);
                Log::info($this->datetime . '历史数据库数据插入成功'.count($params).'条');
            }
            else{
                Log::info($this->datetime . '历史数据库没有数据插入');
            }
        });
    }

    private function mongodb_data(){
        $params = [];
        try{
            $obj_hitorian_factory = (new Historian())->setConnection($this->remote_conn);  //连接电厂内部数据库
            $obj_hitorian_local = (new HistorianData())->setConnection($this->tenement_conn)->setTable($this->local_data_table); //连接特定租户下面的本地数据库表
        }
        catch(Exception $ex){
            Log::info('连接电厂历史数据库异常');
            Log::info(var_export($ex, true));
        }

        $rows = $obj_hitorian_factory->findByDatetime($this->datetime);
        if($rows && count($rows) > 0){
            foreach ($rows as $key => $item) {
                $local_row = $obj_hitorian_local->findRowBySn($item->_id);
                if(!$local_row){
                    //本地不存在则插入
                    $params[] = array(
                        '_id'=>$item['_id'],
                        'tag_name'=>$item['tag_name'],
                        'description'=>$item['description'],
                        'value'=>$item['value'],
                        'datetime'=>$item['datetime'],
                        'created_at' => $item['datetime'],
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                }
            }
        }

        if($params && count($params) > 0){
            $res = $obj_hitorian_local->insertMany($params);
            Log::info($this->datetime . '历史数据库数据插入成功'.count($params).'条');
        }
        else{
            Log::info($this->datetime . '历史数据库没有数据插入');
        }
    }
}
