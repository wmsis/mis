<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;
use App\Jobs\CountDayPowerDataJob;

class CountDayPowerData extends Command
{
    /**
     * 累计每日电表测点的值.
     * @param date 获取数据的日期
     * @var string
     */
    protected $signature = 'count:dayPowerData {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'count dayPowerData';

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
            $orgnization = (new Orgnization())->setConnection($tenement_conn);//连接特定租户下面的组织表
            //循环电厂
            $factories = $orgnization->where('level', 2)->get();
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    $electricity_day_data_table = 'electricity_day_data_' . $factory->code; //本地存储累计日数据库表名称
                    $power_day_data_table = 'power_day_data_' . $factory->code;

                    $params = array(
                        'date' => $date,
                        'tenement_conn' => $tenement_conn,
                        'power_day_data_table' => $power_day_data_table,
                        'electricity_day_data_table' => $electricity_day_data_table
                    );

                    dispatch(new CountDayPowerDataJob($params));
                }
            }
        }
        return 0;
    }
}
