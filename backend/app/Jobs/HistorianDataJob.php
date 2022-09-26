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
    protected $date;
    protected $tenement_conn;
    protected $tenement_mongo_conn;
    protected $remote_conn;
    protected $local_tag_table;
    protected $local_data_table;
    protected $db_type;
    protected $orgnization;
    public $tries = 3;

    /**
    * @param date 获取数据的日期
    * @param connection 获取数据的远程数据库连接
    * @param table 存储数据的本地数据库表
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->date = $params && isset($params['date']) ? $params['date'] : '';
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
            $this->historiandb_data();
        }
        else{
            $this->mongodb_data();
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

        $obj_hitorian_factory->chunk(20, function ($tagslist) use ($obj_hitorian_local) {
            $params = [];
            $tagsNameList = [];
            foreach ($tagslist as $key => $tag) {
                $tagsNameList[] = $tag->tag_name;
            }
            $tagsNameString = implode(';', $tagsNameList);
            $res = HistorianService::currentData($this->orgnization, $tagsNameString);
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
                Log::info($this->date . '历史数据库数据插入成功'.count($params).'条');
            }
            else{
                Log::info($this->date . '历史数据库没有数据插入');
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

        $rows = $obj_hitorian_factory->findByDate($this->date);
        if($rows && count($rows) > 0){
            foreach ($rows as $key => $item) {
                $local_row = $obj_hitorian_local->findRowBySn($item->sn);
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
            Log::info($this->date . '历史数据库数据插入成功'.count($params).'条');
        }
        else{
            Log::info($this->date . '历史数据库没有数据插入');
        }
    }
}
