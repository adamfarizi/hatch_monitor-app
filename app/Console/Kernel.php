<?php

namespace App\Console;

use App\Events\ThingSpeakEvent;
use App\Models\Monitor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Http\Controllers\KontrolAlatController;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:get-thing-speak')->everyMinute();
        // $schedule->command('app:log-harian')->cron('0 7,10,13,16,19 * * *');
        $schedule->command('app:log-harian')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
