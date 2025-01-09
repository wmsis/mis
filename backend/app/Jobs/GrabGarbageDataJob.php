<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ErrorException;
use App\Models\Factory\GrabGarbage as GrabGarbageFactoryModel;  //电厂数据模型
use App\Models\SIS\GrabGarbage as GrabGarbageLocalModel;        //本地数据模型
use Illuminate\Support\Facades\Log;
use Config;

class GrabGarbageDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $tenement_conn;
    protected $remote_conn;
    protected $local_table;
    public $tries = 3;

    /**
     * Create a new job instance.
     * @param date 获取数据的日期
     * @param connection 获取数据的远程数据库连接
     * @param table 存储数据的本地数据库表
     * @return void
     */
    public function __construct($date=null, $tenement_conn=null, $remote_conn=null, $local_table=null)
    {
        $this->date = $date;
        $this->tenement_conn = $tenement_conn;
        $this->remote_conn = $remote_conn;
        $this->local_table = $local_table;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $obj_grab_garbage_factory = (new GrabGarbageFactoryModel())->setConnection($this->remote_conn);  //连接电厂内部数据库
            $obj_grab_garbage_local = (new GrabGarbageLocalModel())->setConnection($this->tenement_conn)->setTable($this->local_table); //连接特定租户下面的本地数据库表
        
            $latest_row = $obj_grab_garbage_local->findLatestReport();
            if($latest_row){
                $timestamp = $latest_row->time;
            }
            else{
                $timestamp = time() - 30 * 24 * 60 * 60;
            }

            $rows = $obj_grab_garbage_factory->select(['*'])
                ->where('time', '>=',$timestamp)
                ->orderBy("time", "ASC")
                ->limit(50)
                ->get();

            $params = [];
            if($rows && count($rows) > 0){
                foreach ($rows as $key => $item) {
                    $local_row = $obj_grab_garbage_local->findRowBySn($item->sn);
                    if(!$local_row){
                        //本地不存在则插入
                        $params[] = array(
                            'allsn'=>$item['allsn'],
                            'sn'=>$item['sn'],
                            'time'=>$item['time'],
                            'che'=>$item['che'],
                            'dou'=>$item['dou'],
                            'liao'=>$item['liao'],
                            'code'=>$item['code'],
                            'lost'=>$item['lost'],
                            'hev'=>$item['hev'],
                            'created_at' => date('Y-m-d H:i:s', intval($item['time'])),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    }
                }
            }

            if($params && count($params) > 0){
                $obj_grab_garbage_local->insertMany($params);
                Log::info($this->date . '恩倍力抓斗数据插入成功'.count($params).'条');
            }
            else{
                Log::info($this->date . '恩倍力抓斗没有数据插入');
            }
        }
        catch(ErrorException $ex){
            Log::info('连接电厂抓斗数据库异常');
            Log::info(var_export($ex, true));
        }
    }
}
