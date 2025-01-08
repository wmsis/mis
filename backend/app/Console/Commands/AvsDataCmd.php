<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AvsDataJob;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ConfigAvsDB;
use Illuminate\Support\Facades\Log;

/**
 * 从电厂获取地磅数据并保存到本地
 */
class AvsDataCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:avsdata {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collect avsdata';

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
            $configDB = (new ConfigAvsDB())->setConnection($tenement_conn); //连接特定租户下面的抓斗数据库配置表
            $orgObj = (new Orgnization())->setConnection($tenement_conn);
            //循环电厂
            $factories = $orgObj->where('level', 2)->get();
            foreach ($factories as $k2 => $factory) {
                Log::info("00000000000");
                if($factory->code){
                    //具体电厂的抓斗数据库配置信息
                    Log::info("11111111111111");
                    $row = $configDB->where('orgnization_id', $factory->id)->first();
                    if($row){
                        Log::info("2222222222222222");
                        $remote_conn =  'avs_' . $tenement->id . '_' . $row->id;   //电厂地磅数据库连接名称
                        $local_table = 'weighbridge_' . $factory->code;            //本地存储数据库表名称
                        $local_format_table = 'weighbridge_format_' . $factory->code;            //本地存储数据库表名称
                        $avs_type = $row->type;

                        dispatch(new AvsDataJob($date, $tenement_conn, $remote_conn, $local_table, $avs_type, $local_format_table));
                    }
                }
            }
        }
        return 0;
    }
}
