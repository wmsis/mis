<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\HistorianDataJob;
use Illuminate\Support\Facades\DB;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ConfigHistorianDB;
use App\Models\Factory\DcsData;
use App\Models\SIS\HistorianTag;
use Log;

class HistorianData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * @param datetime 获取数据的日期
     */
    protected $signature = 'collect:historianData {--datetime=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collect historianData';

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
        $optionDate = $this->option('datetime');
        if($optionDate != 'default'){
            $datetime = $optionDate;
        }
        else{
            $datetime = date('Y-m-d H:i:s', time() - 10 * 60);  ////取十分钟之前的数据
        }

        $tenements = DB::connection('mysql_mis')->table('tenement')->get();
        //循环租户
        foreach ($tenements as $k1 => $tenement) {
            $tenement_conn = $tenement->code; //租户数据库连接名称
            $tenement_mongo_conn =  $tenement->code . '_mongo'; //租户MongoDB数据库连接名称
            $configHistorian = (new ConfigHistorianDB())->setConnection($tenement_conn); //连接特定租户下面的历史数据库配置表
            $orgObj = (new Orgnization())->setConnection($tenement_conn);
            //循环电厂
            $factories = $orgObj->where('level', 2)->get();
            Log::info('GGGGGGGGGGGGGGGGGGG');
            Log::info(var_export($factories, true));
            foreach ($factories as $k2 => $factory) {
                if($factory->code){
                    Log::info('HHHHHHHHHHHHHHHHHHHH');
                    Log::info($factory->code);
                    //具体电厂的历史数据库配置信息
                    $cfg = $configHistorian->where('orgnization_id', $factory->id)->first();
                    if($cfg){
                        Log::info('JJJJJJJJJJJJJJJJJJJJJJ');
                        $local_tag_table = 'historian_tag_' . $factory->code; //本地存储数据库表名称
                        $local_data_table = 'historian_data_' . $factory->code; //本地存储数据库表名称
                        $local_format_data_table = 'historian_format_data_' . $factory->code; //本地存储数据库表名称
                        if($cfg['version'] && $cfg['version'] < 7){
                            $remote_conn =  'historian_' . $tenement->id . '_' . $cfg['id'];       //5.5版本存储在电厂本地MongoDB数据库，电厂历史数据库连接名称
                            $db_type = 'mongodb';
                        }
                        else{
                            $remote_conn =  $tenement_conn;       //7.0以上存在电厂本地historian数据库，电厂历史数据库连接名称,远程直连
                            $db_type = 'historiandb';
                        }

                        $params = array(
                            'datetime' => $datetime,
                            'tenement_conn' => $tenement_conn,
                            'tenement_mongo_conn' => $tenement_mongo_conn,
                            'remote_conn' => $remote_conn,
                            'local_tag_table' => $local_tag_table,
                            'local_data_table' => $local_data_table,
                            'local_format_data_table' => $local_format_data_table,
                            'db_type' => $db_type,
                            'cfgdb' => $cfg->toArray()
                        );

                        dispatch(new HistorianDataJob($params));
                    }
                }
            }
        }
        return 0;
    }
}
