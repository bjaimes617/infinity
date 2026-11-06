<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use App\Jobs\GeneralDailyTasksJob;
use App\Jobs\ProcessAsistenciaExpedientesJob;
use App\Jobs\ProcessDataProfit;
use App\Jobs\Store_data_chats_personal;

class DispatchDailyQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:dispatchDailyQueue {--sync : Ejecuta los jobs secuencialmente de forma síncrona sin esperar al worker de colas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepara y despacha la cadena de jobs diarios a la cola (o los ejecuta inmediatamente con --sync)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('sync'))
        {
            $this->info('[Síncrono] Iniciando ejecución secuencial de jobs diarios...');

            $this->line('-> Ejecutando GeneralDailyTasksJob...');
            dispatch_sync(new GeneralDailyTasksJob());

            $this->line('-> Ejecutando ProcessAsistenciaExpedientesJob...');
            dispatch_sync(new ProcessAsistenciaExpedientesJob());

            $this->line('-> Ejecutando Store_Data_profit...');
            dispatch_sync(new ProcessDataProfit());

            $this->line('-> Actualizando metricas del personal');
            dispatch_sync(new Store_data_chats_personal());

            $this->info('[Síncrono] Todos los jobs diarios finalizaron.');
            return 0;
        }

        $this->info('[Cola] Encolando cadena de jobs diarios (Bus::chain)...');

        Bus::chain([
            new GeneralDailyTasksJob(),
            new ProcessDataProfit(),
            new ProcessAsistenciaExpedientesJob(),
            new Store_data_chats_personal()
        ])->dispatch();

        $this->info('[Cola] Cadena de jobs encolada exitosamente. Se procesará por el queue worker.');
        return 0;
    }
}
