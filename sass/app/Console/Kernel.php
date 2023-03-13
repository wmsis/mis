<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\AlarmData;
use App\Console\Commands\CountUserPoint;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        //AlarmData::class,
        CountUserPoint::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('alarm:data')->everyMinute();
        $schedule->command('count:userPoint --type=electricity')->everyMinute();
        $schedule->command('count:userPoint --type=tag')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
