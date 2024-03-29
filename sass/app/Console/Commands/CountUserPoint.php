<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UserPointJob;
use Log;

class CountUserPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:userPoint {--type=electricity} {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'count userPoint';

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
        $type = $this->option('type');
        $date = $this->option('date');
        $date = $date != 'default' ? $date : date('Y-m-d');
        dispatch(new UserPointJob($type, $date));
        return 0;
    }
}
