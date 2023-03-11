<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use CheckService;
use Log;

class UserPointJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type=null)
    {
        $this->type = $type ? $type : 'electricity';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type == 'electricity'){
            $this->electricityData();
        }
    }

    private function electricityData(){
        $date = date('Y-m-d');
        $final = CheckService::userClassPoint($date);

        Log::info('22222222222222222');
        Log::info(var_export($final, true));
    }
}
