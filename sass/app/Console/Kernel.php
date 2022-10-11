<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CountDayDcsData;
use App\Console\Commands\CountDayElectricityData;
use App\Console\Commands\CountDayGrabGarbageData;
use App\Console\Commands\CountDayWeighBridgeData;
use App\Console\Commands\CountDayPowerData;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        CountDayDcsData::class,
        CountDayElectricityData::class,
        CountDayGrabGarbageData::class,
        CountDayWeighBridgeData::class,
        CountDayPowerData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('count:dayElectricityData')->everyMinute();
        $schedule->command('count:dayDcsData')->everyMinute();
        $schedule->command('count:dayGrabGarbageData')->everyMinute();
        $schedule->command('count:dayWeighBridgeData')->everyMinute();
        $schedule->command('count:dayPowerData')->everyMinute();
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
