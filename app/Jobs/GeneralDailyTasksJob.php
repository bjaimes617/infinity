<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GeneralDailyTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Tiempo de espera máximo en segundos (10 minutos).
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('[GeneralDailyTasksJob] Iniciando tareas generales...');

        Artisan::call('infinity:inactivevisitantes');
        Artisan::call('infinity:convocadoscarteras');
        Artisan::call('infinity:deletevisitantes');
        Artisan::call('infinity:cursosactive');

        Log::info('[GeneralDailyTasksJob] Tareas generales finalizadas exitosamente.');
    }
}
