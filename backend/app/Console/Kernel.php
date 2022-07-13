<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ExcelReader;
use App\Console\Commands\ExcelWriter;
use App\Console\Commands\ExportEconomyDaily;
use App\Console\Commands\CollectIEC104Data;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\RecordAlarm::class,
        ExcelReader::class,
        ExcelWriter::class,
        ExportEconomyDaily::class,
        CollectIEC104Data::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        #$schedule->command('alarm:record')->everyMinute();
        #$schedule->command('export:economydaily')->everyMinute();
        #$schedule->command('export:classstatement')->everyMinute();
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
