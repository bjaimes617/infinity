<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\InactiveVisitantes;
use App\Console\Commands\DeleteVisitantes;
use App\Console\Commands\ConvocadosAsistenciaRun;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Prepara y encola la cadena de tareas diarias (General -> Asistencia/Expedientes -> Profit)
        // Se despacha a las 04:00 AM para que la cola las procese en orden secuencial
        $schedule->command('infinity:dispatchDailyQueue >> storage/logs/queue/dispatchDailyQueue.log 2>&1')->dailyAt('03:50');
        $schedule->command('queue:work --stop-when-empty >> storage/logs/queue/queue.log 2>&1')->dailyAt('04:00');

        // Desactivar trimestres ARI (mensual)
        $schedule->command('infinity:disabletrimestre')->monthlyOn(1, '05:00');
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
