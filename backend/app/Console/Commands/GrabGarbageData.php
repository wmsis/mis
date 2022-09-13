<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GrabGarbageDataJob;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ConfigGarbageDB;
use Log;

class GrabGarbageData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * @param date 获取数据的日期
     * @param connection 获取数据的远程数据库连接
     * @param table 存储数据的本地数据库表
     */
    protected $signature = 'collect:grabGarbageData {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collect grabGarbageData';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 循环个租户下面的歌电厂，每个电厂的数据一个队列取数据
     *
     * @return int
     */
    public function handle()
    {
        $optionDate = $this->option('date');
        if($optionDate != 'default'){
            $date = $optionDate;
        }
        else{
            $date = date('Y-m-d');
        }

        $tenements = DB::connection('mysql_mis')->table('tenement')->get();
        //循环租户
        foreach ($tenements as $k1 => $tenement) {
            $tenement_conn = $tenement->code; //租户数据库连接名称
            $configGarbage = (new ConfigGarbageDB())->setConnection($tenement_conn); //连接特定租户下面的抓斗数据库配置表
            $orgObj = (new Orgnization())->setConnection($tenement_conn);
            //循环电厂
            $factories = $orgObj->where('level', 3)->get();
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    //具体电厂的抓斗数据库配置信息
                    $row = $configGarbage->where('orgnization_id', $factory->id)->first();
                    if($row){
                        $remote_conn =  'garbage_' . $tenement->id . '_' . $row->id;       //电厂抓斗数据库连接名称
                        $local_table = 'grab_garbage_' . $factory->code; //本地存储数据库表名称
                        dispatch(new GrabGarbageDataJob($date, $tenement_conn, $remote_conn, $local_table));
                    }
                }
            }
        }
        return 0;
    }
}
