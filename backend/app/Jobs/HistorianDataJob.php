<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Factory\DcsData;   //电厂数据模型
use App\Models\Mongo\HistorianData; //本地数据模型
use App\Models\SIS\HistorianTag;    //本地数据标签模型
use App\Models\Mongo\HistorianFormatData; //本地格式化数据模型
use App\Models\SIS\DcsMap;
use App\Models\SIS\DcsStandard;
use Log;
use Config;
use HistorianService;
use MongoDB\BSON\UTCDateTime;

class HistorianDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $datetime;
    protected $tenement_conn; //租户连接
    protected $tenement_mongo_conn; //本地mongo连接
    protected $remote_conn; //电厂连接
    protected $local_tag_table; //historian_tag
    protected $local_data_table; //本地保存的MongoDB原始数据集合
    protected $local_format_data_table; //本地保存的格式化后的数据集合
    protected $db_type; //数据库类型mongo或者historian
    protected $cfgdb;//数据库配置信息
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
        $this->local_format_data_table = $params && isset($params['local_format_data_table']) ? $params['local_format_data_table'] : '';
        $this->db_type = $params && isset($params['db_type']) ? $params['db_type'] : '';
        $this->cfgdb = $params && isset($params['cfgdb']) ? $params['cfgdb'] : '';
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

    //从远程historian7.0获取数据
    protected function historiandb_data(){
        try{
            $obj_hitorian_factory = (new HistorianTag())->setConnection($this->tenement_conn)->setTable($this->local_tag_table);  //连接电厂数据库
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
            $res = HistorianService::SampledData($this->cfgdb, $tagsNameString, $start, $end, $count, $samplingMode, $calculationMode, $intervalMS);
            if($res && $res['code'] === 0 && $res['data']['ErrorCode'] === 0){
                $datalist = $res['data']['Data'];
                foreach ($datalist as $key => $item) {
                    $value = '';
                    if(isset($item['Samples']) && $item['Samples'] && count($item['Samples']) > 0) {
                        $timestamp = $item['Samples'][0]['TimeStamp'];
                        $value = $item['Samples'][0]['Value'];
                        if(strtolower($value) == 'false'){
                            $value = 0;
                        }
                        else if(strtolower($value) == 'true'){
                            $value = 1;
                        }
                    }
                    $local_row = $obj_hitorian_local->findRowByTagAndTime($item['TagName'], $this->datetime);
                    if(!$local_row){
                        //本地不存在则插入
                        $params[] = array(
                            'tag_name' => $item['TagName'],
                            'value'=> $value,
                            'datetime'=> $this->datetime,
                            'created_at' => $this->datetime,
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                }
            }

            if($params && count($params) > 0){
                $res = $obj_hitorian_local->insertMany($params);
                //Log::info($this->datetime . '历史数据库数据插入成功'.count($params).'条');
            }
            else{
                //Log::info($this->datetime . '历史数据库没有数据插入');
            }
        });

        $this->historian_format_data();
    }

    //从远程MongoDB获取数据（historian5.5读取不方便转为opc读取并转存到电厂本地MongoDB）
    protected function mongodb_data(){
        try{
            $obj_hitorian_factory = (new DcsData())->setConnection($this->remote_conn);  //连接电厂内部数据库
            $obj_hitorian_local = (new HistorianData())->setConnection($this->tenement_mongo_conn)->setTable($this->local_data_table); //连接特定租户下面的本地数据库表
        }
        catch(Exception $ex){
            Log::info('连接电厂历史数据库异常');
            Log::info(var_export($ex, true));
        }

        $begin = date('Y-m-d H:i', strtotime($this->datetime)) . ':00'; //获取一分钟内的数据
        $end = date('Y-m-d H:i', strtotime($this->datetime)) . ':59';
        $start = new UTCDateTime(strtotime($begin)*1000);
        $stop = new UTCDateTime(strtotime($end)*1000);
        Log::info('GGGGGGGGGGGGGGGGGGG');
        $obj_hitorian_factory->select(['tag_name', 'datetime', 'value'])
            ->whereBetween('datetime', array($start, $stop))
            ->chunk(100, function ($rows) use ($obj_hitorian_local) {
                Log::info('HHHHHHHHHHHHHHHHHHHH');
            $params = [];
            $stack = [];
            if($rows && count($rows) > 0){
                foreach ($rows as $key => $item) {
                    if(in_array($item->tag_name, $stack)){
                        continue;
                    }
                    $stack[] = $item->tag_name;

                    if($item->tag_name == 'Applications.GuoLu1.TE208'){
                        Log::info('000000000000000');
                        Log::info($item->value);
                    }
                    $local_row = $obj_hitorian_local->findRowByTagAndTime($item->tag_name, $this->datetime);
                    if(!$local_row){
                        //本地不存在则插入
                        $params[] = array(
                            'tag_name' => $item->tag_name,
                            'value'=> $item->value,
                            'datetime'=> $this->datetime,
                            'created_at' => $this->datetime,
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                }
            }

            if($params && count($params) > 0){
                $obj_hitorian_local->insertMany($params);
                //Log::info($this->datetime . '历史数据库数据插入成功'.count($params).'条');
            }
            else{
                //Log::info($this->datetime . '历史数据库没有数据插入');
            }
        });
        Log::info('JJJJJJJJJJJJJJJJJJJ');
        $this->historian_format_data();
        Log::info('KKKKKKKKKKKKKKKKKKKKKK');
    }

    //根据DCS标准名称格式化获取到的数据
    protected function historian_format_data(){
        Log::info('11111111111111111');
        //获取映射关系
        //本租户下面某个电厂的DCS映射关系
        $map_lists = (new DcsMap())->setConnection($this->tenement_conn)->where('orgnization_id', $this->cfgdb['orgnization_id'])->get();
        foreach ($map_lists as $k1 => $item) {
            //找到每个映射关系绑定的tagid
            $ids = explode(',', $item->tag_ids);
            $tag_key_values = [];
            $obj_hitorian_factory = (new HistorianTag())->setConnection($this->tenement_conn)->setTable($this->local_tag_table);
            $taglists = $obj_hitorian_factory->whereIn('id', $ids)->get();
            if($taglists &&  count($taglists) > 0){
                $tagname_arr = [];  //所有tagname列表
                foreach ($taglists as $key => $tag) {
                    $tagname_arr[] = $tag->tag_name;
                    //初始化键值对
                    $tag_key_values[$tag->tag_name] = array(
                        'value' => 0
                    );
                }

                //本地保存的数据库
                $obj_hitorian_local = (new HistorianData())->setConnection($this->tenement_mongo_conn)->setTable($this->local_data_table);
                $tags_data = $obj_hitorian_local->whereIn('tag_name', $tagname_arr)->where('datetime', $this->datetime)->get();
                foreach ($tags_data as $key => $tag) {
                    if($tag->tag_name == 'Applications.GuoLu1.TE208'){
                        Log::info('22222222222222222222');
                        Log::info($tag->value);
                    }

                    if(strpos($tag->value, '.') !== false){
                        $tag->value = number_format($tag->value, 2);
                    }

                    $tag_key_values[$tag->tag_name] = array(
                        'value' => $tag->value
                    );
                }
            }

            //取值成功
            if(!empty($tag_key_values)){
                $val = 0;
                if($item->func){
                    //计算函数的值
                    $str = $item->func;
                    foreach ($tag_key_values as $tag_name => $tag) {
                        $str = str_replace('[' . $tag_name . ']', $tag['value'], $str);
                    }
                    $val = eval("return $str;");
                }
                else{
                    foreach ($tag_key_values as $tag_name => $tag) {
                        $val = (float)$tag['value']; //取第一个tag的值
                        if($tag_name == 'Applications.GuoLu1.TE208'){
                            Log::info('2222222222222222222222');
                            Log::info($tag['value']);
                            Log::info($val);
                        }
                        break;
                    }
                }
                //本地格式化数据
                $obj_hitorian_format = (new HistorianFormatData())->setConnection($this->tenement_mongo_conn)->setTable($this->local_format_data_table);
                $local_row = $obj_hitorian_format->findRowByIdAndTime($item->dcs_standard_id, $this->datetime);
                if(!$local_row){
                    //本地不存在则插入
                    $obj_hitorian_format->dcs_standard_id = $item->dcs_standard_id;
                    $obj_hitorian_format->value = $val;
                    $obj_hitorian_format->datetime = $this->datetime;
                    $obj_hitorian_format->created_at = $this->datetime;
                    $obj_hitorian_format->updated_at = date('Y-m-d H:i:s');
                    $obj_hitorian_format->save();
                }
            }
        }
    }
}
