<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\IEC104Data;
use App\Console\Commands\GrabGarbageData;
use App\Console\Commands\HistorianData;
use App\Console\Commands\CountDayDcsData;
use App\Console\Commands\CountDayElectricityData;
use App\Console\Commands\CountDayGrabGarbageData;
use App\Console\Commands\CountDayWeighBridgeData;
use App\Console\Commands\CountDayPowerData;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        IEC104Data::class,
        GrabGarbageData::class,
        HistorianData::class,
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
        //收集采集数据
        $yestoday = date('Y-m-d', time() - 24 * 60 * 60);
        $yestoday_cmd = 'collect:grabGarbageData --date=' . $yestoday;
        $schedule->command('collect:iec104data')->everyFiveMinutes();
        $schedule->command('collect:grabGarbageData')->hourlyAt(30);//当天的数据  每小时第30分钟
        $schedule->command($yestoday_cmd)->twiceDaily(1, 22);//前一天的数据
        $schedule->command('collect:historianData')->everyFiveMinutes();

        //每日累计数据
        $schedule->command('count:dayElectricityData')->everyMinute();
        $schedule->command('count:dayDcsData')->everyMinute();
        $schedule->command('count:dayGrabGarbageData')->everyMinute();
        $schedule->command('count:dayWeighBridgeData')->everyMinute();
        $schedule->command('count:dayPowerData')->everyMinute();

        //累计前一天的数据
        $yestoday = date('Y-m-d', time() - 24 * 60 * 60);
        $electricity_cmd = 'count:dayElectricityData --date=' . $yestoday;
        $schedule->command($electricity_cmd)->twiceDaily(1, 22);

        $dcs_cmd = 'count:dayDcsData --date=' . $yestoday;
        $schedule->command($dcs_cmd)->twiceDaily(1, 22);

        $grab_garbage_cmd = 'count:dayGrabGarbageData --date=' . $yestoday;
        $schedule->command($grab_garbage_cmd)->twiceDaily(2, 23);

        $weigh_bridge_cmd = 'count:dayWeighBridgeData --date=' . $yestoday;
        $schedule->command($weigh_bridge_cmd)->twiceDaily(1, 22);

        $power_cmd = 'count:dayPowerData --date=' . $yestoday;
        $schedule->command($power_cmd)->twiceDaily(2, 23);
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
