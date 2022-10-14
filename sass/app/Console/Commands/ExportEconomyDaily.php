<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ExportEconomyDailyJob;
use App\Models\SIS\Orgnization;
use Illuminate\Support\Facades\DB;

class ExportEconomyDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:dailyData {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'export dailyData';

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

        //今天及今天以前才会有数据
        if(strtotime($date . ' 00:00:00') < time()){
            $tenements = DB::connection('mysql_mis')->table('tenement')->get();
            //循环租户
            foreach ($tenements as $k1 => $tenement) {
                $tenement_conn = $tenement->code; //租户数据库连接名称
                $orgnization = (new Orgnization())->setConnection($tenement_conn);//连接特定租户下面的组织表
                //循环电厂
                $factories = $orgnization->where('level', 2)->get();
                foreach ($factories as $k2 => $factory) {
                    if($factory->code){
                        $params = array(
                            'date' => $date,
                            'tenement_conn' => $tenement_conn,
                            'factory' => $factory
                        );

                        dispatch(new ExportEconomyDailyJob($params));
                    }
                }
            }
        }
        return 0;
    }
}
