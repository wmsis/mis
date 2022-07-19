<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GrabGarbageDataJob;

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
    protected $signature = 'collect:grabGarbageData {--date=default} {--connection=default} {--table=default}';

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

        $optionConnection = $this->option('connection');
        if($optionConnection != 'default'){
            $connection = $optionConnection;
        }
        else{
            $connection = 'mysql_yongqiang2_grab_garbage';
        }

        $optionTable = $this->option('table');
        if($optionTable != 'default'){
            $table = $optionTable;
        }
        else{
            $table = 'grab_garbage_yongqiang2';
        }

        dispatch(new GrabGarbageDataJob($date, $connection, $table));
        return 0;
    }
}
