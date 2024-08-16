<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Actions\Instances\ShutdownInstances;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // schedule instances shutdown
        $schedule->command('uhclabs:si')->everyMinute()->name('shutdown_instances')->withoutOverlapping();
        $schedule->command('generate:ids-old-scores')->everyMinute()->name('generate:ids-old-scores')->withoutOverlapping();
        $schedule->command('generate:scores')->everyMinute()->name('generate:scores')->withoutOverlapping();

        $schedule->command('uhclabs:releaseMachine')->dailyAt('18:00');
        $schedule->command('uhclabs:removeFreemium')->dailyAt('18:00');
        $schedule->command('uhclabs:retireMachine')->dailyAt('18:00');
        $schedule->command('uhclabs:syncactivecampaignsubscriptions')->hourly();
        // cria uma nova subscription no plano free para cada usuário que cancelou a assinatura antiga.
        // $schedule->command('subscriptions:update-ended')->hourly();
        $schedule->command('stripe:syncsubscriptions')->daily();
        $schedule->command('billing:check_user_subscription')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
