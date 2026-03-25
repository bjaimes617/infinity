<?php

namespace App\Console\Commands;

use App\Exports\Personal\PersonalCecosExport;
use App\Models\personal\PersonalModel;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ReplicaExpedientesDiarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:replicaExpedientesDiarios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un CSV con los expedientes del personal con frecuencia diaria';

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
        $this->alert('Iniciando replica de expedientes diarios');
        $fechaactual = Carbon::now();

        $dia = $fechaactual->copy()->subdays(1)->format('d');
        $mes = $fechaactual->copy()->subdays(1)->format('m');   
        $anio = $fechaactual->copy()->subdays(1)->format('Y');

        $result = PersonalModel::where('area_trabajo_id', '2')->orderby('primer_nombre', 'ASC')->get();

        if (!Storage::disk('replicalp')->exists($anio.'/'.$mes.'/'.$anio.'_'.$mes.'_'.$dia.'_ReplicaExpedientesDiarios.csv')) {            
            Excel::store(new PersonalCecosExport($result), $anio.'/'.$mes.'/'.$anio.'_'.$mes.'_'.$dia.'_ReplicaExpedientesDiarios.csv', 'replicalp');
            $this->info('Replica de expedientes diarios finalizada');
            return true;
        } else {    
            $this->info('Ya existe una replica de expedientes almacenada');
            return false;
        }
    }
}
