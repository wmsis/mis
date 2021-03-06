<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Models\Factory\GrabGarbage as GrabGarbageFactoryModel;  //电厂数据模型
use App\Http\Models\SIS\GrabGarbage as GrabGarbageLocalModel;        //本地数据模型
use Log;

class GrabGarbageDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    protected $conn;
    protected $tb;
    public $tries = 3;

    /**
     * Create a new job instance.
     * @param date 获取数据的日期
     * @param connection 获取数据的远程数据库连接
     * @param table 存储数据的本地数据库表
     * @return void
     */
    public function __construct($date=null, $connection=null, $table=null)
    {
        $this->date = $date;
        $this->conn = $connection;
        $this->tb = $table;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $params = [];
        $obj_grab_garbage_factory = (new GrabGarbageFactoryModel())->setConnection($this->conn);
        $obj_grab_garbage_local = (new GrabGarbageLocalModel())->setTable($this->tb);
        $rows = $obj_grab_garbage_factory->findByDate($this->date);
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
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );
                }
            }
        }

        if($params && count($params) > 0){
            $res = $obj_grab_garbage_local->insertMany($params);
            Log::info($this->date . '恩倍力抓斗数据插入成功'.count($params).'条');
        }
        else{
            Log::info($this->date . '恩倍力抓斗数据插入失败');
        }
    }
}
