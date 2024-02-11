<?php

namespace App\Console;

use App\Console\Commands\DeleteTempUploadedFiles;
use App\Jobs\CheckProductExpiry;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('backup:run')->daily()->at('01:00');
        $schedule->command(DeleteTempUploadedFiles::class)->hourly();

//        $schedule->job(new CheckProductExpiry)->daily();
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
