<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\Orgnization;
use App\Models\Mongo\HistorianData;
use App\Models\Mongo\HistorianFormatData;

class RemoveDcsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:dcsData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove dcsData';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenements = DB::connection('mysql_mis')->table('tenement')->get();
        //循环租户
        foreach ($tenements as $k1 => $tenement) {
            $tenement_conn = $tenement->code; //租户数据库连接名称
            $tenement_mongo_conn =  $tenement->code . '_mongo'; //租户MongoDB数据库连接名称

            $orgObj = (new Orgnization())->setConnection($tenement_conn);
            //循环电厂
            $factories = $orgObj->where('level', 2)->get();
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    $local_data_table = 'historian_data_' . $factory->code; //本地存储数据库表名称
                    $local_format_data_table = 'historian_format_data_' . $factory->code; //本地存储数据库表名称
                    $obj_hitorian_local = (new HistorianData())->setConnection($tenement_mongo_conn)->setTable($local_data_table);
                    $obj_hitorian_format = (new HistorianFormatData())->setConnection($tenement_mongo_conn)->setTable($local_format_data_table);
                    $oneDayAgo = now()->subDay(); // 获取当前时间减去1天的时间点
                    $obj_hitorian_local->where("datetime", "<=", $oneDayAgo)->orderBy("datetime", 'desc')->limit(1000)->delete();
                    $obj_hitorian_format->where("datetime", "<=", $oneDayAgo)->orderBy("datetime", 'desc')->limit(1000)->delete();
                }
            }
        }
        return 0;
    }
}