<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\ConfigElectricityDB;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ElectricityMap;
use App\Models\SIS\Electricity;
use Log;

class RemoveElectricityData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:electricityData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove electricityData';

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
            $config_electricity = (new ConfigElectricityDB())->setConnection($tenement_conn); //连接特定租户下面的历史数据库配置表
            $electricity_map = (new ElectricityMap())->setConnection($tenement_conn);
            $orgObj = (new Orgnization())->setConnection($tenement_conn);
            //循环电厂
            $factories = $orgObj->where('level', 2)->get();
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    //具体电厂的历史数据库配置信息
                    $cfg = $config_electricity->where('orgnization_id', $factory->id)->first();
                    $map = $electricity_map->where('orgnization_id', $factory->id)->get();
                    if($cfg && $map){
                        $local_data_table = 'electricity_' . $factory->code; //本地存储数据库表名称
                        $electricity = (new Electricity())->setConnection($tenement_conn)->setTable($local_data_table);
                        $compare_date = date('Y-m-d H:i:s', $begin_timestamp - 30 * 24 * 60 * 60);
                        $electricity->where("created_at", "<=", $compare_date)->delete();
                    }
                }
            }
        }
        return 0;
    }
}