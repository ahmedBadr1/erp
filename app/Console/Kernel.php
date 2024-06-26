<?php

namespace App\Console;

use App\Console\Commands\Check;
use App\Console\Commands\DeleteTempUploadedFiles;
use App\Jobs\CheckProductExpiry;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('backup:run')->daily()->at('02:30')->timezone('Africa/Cairo');
//        $schedule->command(DeleteTempUploadedFiles::class)->hourly();
//        $schedule->job(new CheckProductExpiry)->everyMinute();
        $schedule->command(Check::class)->daily()->at('01:30')->timezone('Africa/Cairo');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
