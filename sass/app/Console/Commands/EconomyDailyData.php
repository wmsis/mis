<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\EconomyDailyDataJob;

class EconomyDailyData extends Command
{
    /**
     * 收集日报数据，以天为单位
     *
     * @var string
     */
    protected $signature = 'collect:dailyData {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collect dailyData';

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

        dispatch(new EconomyDailyDataJob($date));
        return 0;
    }
}
