<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\EconomyDailyExport;
use Log;

class ExportEconomyDaily extends Command
{
    protected $signature = 'export:economydaily {--date=default}';
    protected $description = 'export economydaily';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $optionDate = $this->option('date');
        if($optionDate != 'default'){
            $date = $optionDate;
        }
        else{
            $date = date('Y-m-d');
        }

        dispatch(new EconomyDailyExport($date));
        return 0;
    }
}
