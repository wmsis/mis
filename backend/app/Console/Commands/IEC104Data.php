<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\IEC104DataJob;

class IEC104Data extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:iec104data {--factory=yongqiang2}';

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
        $optionFactory = $this->option('factory');
        if($optionFactory != 'default'){
            $factory = $optionFactory;
        }
        else{
            $factory = 'yongqiang2';
        }

        dispatch(new IEC104DataJob($factory));
        return 0;
    }

}
