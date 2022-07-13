<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\IEC104Data;
use Log;

class CollectIEC104Data extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:iec104data {--date=default}';

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
        $optionDate = $this->option('date');
        if($optionDate != 'default'){
            $date = $optionDate;
        }
        else{
            $date = date('Y-m-d');
        }
        Log::info('0000000000000000000');

        dispatch(new IEC104Data($date));
        return 0;
    }
}
