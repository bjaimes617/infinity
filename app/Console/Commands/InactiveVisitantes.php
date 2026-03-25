<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\personal\VisitantesModel;
use Carbon\Carbon;

class InactiveVisitantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:inactivevisitantes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactiva los Visitantes, segun la fecha de expiración en BD';

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
     * @return mixed
     */
    public function handle()
    {
        
        $now =date('Y-m-d', strtotime(now().'-1 Days'));
        
        if(VisitantesModel::whereBetween('created_at', array("$now 00:00:00", "$now 23:59:59"))->exists()){
            $this->line('EXISTEN REGISTROS DE VISITANTES '.date('d/m/Y', strtotime($now)));
            sleep(1);
            $this->line('.');
            sleep(1);
            $this->line('..');
            sleep(1);
            $this->line('...');
            $this->line('[===========ELIMINANDO VISITANTES DE LA PLATAFORMA===========]');
            sleep(1);            
            VisitantesModel::whereBetween('created_at', array("$now 00:00:00", "$now 23:59:59"))->forcedelete();           
            $this->line('[===================REGISTROS PROCESADOS======================]');
            
        } else {
            $this->line('No Existen Registros con Fecha de Expiracion : '.date('d/m/Y', strtotime($now)));
            sleep(1);
            $this->line('[===========TODO EN ORDEN===========]');
        }
                
    }
}
