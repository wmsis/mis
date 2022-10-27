<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;
use App\Jobs\AlarmDataJob;

class AlarmData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alarm:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'alarm data';

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
            $orgnization = (new Orgnization())->setConnection($tenement_conn);//连接特定租户下面的组织表
            //循环电厂
            $factories = $orgnization->where('level', 2)->get();
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    $historian_format_data_table = 'historian_format_data_' . $factory->code; //本地存储数据库表名称
                    $historian_day_data_table = 'historian_day_data_' . $factory->code; //本地存储累计日数据库表名称

                    $params = array(
                        'factory' => $factory,
                        'tenement_conn' => $tenement_conn,
                        'tenement_mongo_conn' => $tenement_mongo_conn,
                        'historian_format_data_table' => $historian_format_data_table,
                        'historian_day_data_table' => $historian_day_data_table
                    );

                    dispatch(new AlarmDataJob($params));
                }
            }
        }
        return 0;
    }
}
