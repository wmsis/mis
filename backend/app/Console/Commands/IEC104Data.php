<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\IEC104DataJob;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\ConfigElectricityDB;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ElectricityMap;

class IEC104Data extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:iec104data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collect iec104data';


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
                        $params = array(
                            'tenement_conn' => $tenement_conn,
                            'local_data_table' => $local_data_table,
                            'map' => $map->toArray(),
                            'cfgdb' => $cfg->toArray()
                        );

                        dispatch(new IEC104DataJob($params));
                    }
                }
            }
        }
        return 0;
    }

}
