<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Определите команды приложения.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GenerateSitemap::class,
    ];

    /**
     * Определите расписание выполнения команд.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ежедневная генерация sitemap в 3:00 ночи
        $schedule->command('sitemap:generate')->dailyAt('03:00');

        // Альтернативные варианты:
        // $schedule->command('sitemap:generate')->everySixHours();
        // $schedule->command('sitemap:generate')->weekly();
        // $schedule->command('sitemap:generate')->weeklyOn(1, '03:00'); // Каждый понедельник в 3:00

        // Для тестирования (каждые 5 минут)
        // $schedule->command('sitemap:generate')->everyFiveMinutes();
    }

    /**
     * Зарегистрируйте команды для приложения.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
