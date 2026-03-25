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
        
        //$diafinal = carbon::now()->endOfMonth()->format('d');         
        $schedule->command('infinity:inactiveVisitantes')->dailyAt('04:00');
        $schedule->command('infinity:convocadoscarteras')->dailyAt('04:00');
        $schedule->command('infinity:deletevisitantes')->dailyAt('04:00');
        $schedule->command('infinity:cursosactive')->dailyAt('04:00');        
        //$schedule->command('infinity:sendmailscondominios')->dailyAt('04:00');        
        $schedule->command('infinity:InsertNLAsistenciaOP')->dailyAt('04:00');
        $schedule->command('infinity:replicaExpedientesDiarios')->dailyAt('04:20');
        //ejecutara el 1er periodo de nominas
        $schedule->command('RunProfit')->dailyAt('04:40');
        //ejecutara el 2do periodo de nominas
        //$schedule->command('RunProfit')->monthlyOn($diafinal,'04:20');
       //->hourly();
        //desactivar trimestres ARI
        $schedule->command('infinity:disabletrimestre')->monthlyOn(01,'05:00');
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
