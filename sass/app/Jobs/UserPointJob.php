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
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type=null, $date=null)
    {
        $this->type = $type;
        $this->date = $date;
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
        else{
            $this->tagData();
        }
    }

    private function electricityData(){
        CheckService::userClassPoint($this->date);
    }

    private function tagData(){
        CheckService::userTagPoint($this->date);
    }
}
