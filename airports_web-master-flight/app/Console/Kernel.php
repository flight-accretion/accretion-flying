<?php

namespace FlyingCalculation\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\PointsUpdate::class,
        Commands\SyncAirports::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
			$schedule->command('pointsemail')
                 ->hourly();			
			$schedule->command('airports:sync --country=IN')
                 ->dailyAt('02:00')
                 ->withoutOverlapping();
    }
}
