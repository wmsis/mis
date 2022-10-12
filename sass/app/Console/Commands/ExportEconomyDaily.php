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

        dispatch(new ExportEconomyDailyJob($date));
        return 0;
    }
}
