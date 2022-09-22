<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\IEC104Data;
use App\Console\Commands\GrabGarbageData;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        IEC104Data::class,
        GrabGarbageData::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $yestoday = date('Y-m-d', time() - 24 * 60 * 60);
        $yestoday_cmd = 'collect:grabGarbageData --date=' . $yestoday;
        //$schedule->command('collect:iec104data')->everyTenMinutes();
        //$schedule->command('collect:grabGarbageData')->twiceDaily(12, 23);//当天的数据
        //$schedule->command($yestoday_cmd)->twiceDaily(13, 16);//前一天的数据
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
