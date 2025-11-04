<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\Admin\FreelanceController;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            // Pastikan method tarikData bersifat public dan static atau gunakan instance controller
            // Jika tarikData membutuhkan dependency injection, sebaiknya gunakan command artisan khusus untuk penarikan data
            // app()->make() memastikan instansiasi controller dengan dependency yang tepat
            app()->make(FreelanceController::class)->tarikData();
        })->dailyAt('09:00');
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
