<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;
use App\Jobs\CountDayDcsDataJob;

class CountDayDcsData extends Command
{
    /**
     * 累计每日DCS测点的值
     * @param date 获取数据的日期
     * @var string
     */
    protected $signature = 'count:dayDcsData {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'count dayDcsData';

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
            $tenement_mongo_conn =  $tenement->code . '_mongo'; //租户MongoDB数据库连接名称
            $orgnization = (new Orgnization())->setConnection($tenement_conn);//连接特定租户下面的组织表
            //循环电厂
            $factories = $orgnization->where('level', 2)->get();
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    $historian_format_data_table = 'historian_format_data_' . $factory->code; //本地存储数据库表名称
                    $historian_day_data_table = 'historian_day_data_' . $factory->code; //本地存储累计日数据库表名称

                    $params = array(
                        'date' => $date,
                        'tenement_conn' => $tenement_conn,
                        'tenement_mongo_conn' => $tenement_mongo_conn,
                        'historian_format_data_table' => $historian_format_data_table,
                        'historian_day_data_table' => $historian_day_data_table
                    );

                    dispatch(new CountDayDcsDataJob($params));
                }
            }
        }
        return 0;
    }
}
