<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessAsistenciaExpedientesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Tiempo de espera máximo en segundos (20 minutos).
     *
     * @var int
     */
    public $timeout = 1200;
    public $memory_limit = 2048;
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
        Log::info('[ProcessAsistenciaExpedientesJob] Ejecutando infinity:InsertNLAsistenciaOP...');
        Artisan::call('infinity:InsertNLAsistenciaOP');

        Log::info('[ProcessAsistenciaExpedientesJob] Ejecutando infinity:replicaExpedientesDiarios...');
        Artisan::call('infinity:replicaExpedientesDiarios');

        Log::info('[ProcessAsistenciaExpedientesJob] Finalizado exitosamente.');
    }
}
