<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\personal\VisitantesModel;

class DeleteVisitantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:deletevisitantes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'eliminara los visitantes pasado un mes de la fecha de expiracion';

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
        
        $begin = date('Y-m', strtotime(now()."- 1 month"));          
        //a la fecha actual se le quita un mes para buscar los registros inactivos         
        if(VisitantesModel::where('active',0)->whereBetween('expiracion',array("$begin-01", "$begin-31"))->exists()){
            $this->line('EXISTEN REGISTROS DE VISITANTES QUE SUPERAN EL MES '.$begin);
            sleep(1);
            $this->line('.');
            sleep(1);
            $this->line('..');
            sleep(1);
            $this->line('...');
            $this->line('[===========ELIMINANDO VISITANTES DE LA PLATAFORMA===========]');
            sleep(1);            
            VisitantesModel::where('active',0)->whereBetween('expiracion',array("$begin-01", "$begin-31"))->delete();
            $this->line('[===================REGISTROS ELIMINADOS======================]');
        }else {
            $this->line('No Existen Registros con Fecha de Expiracion : '.$begin);
            sleep(1);
            $this->line('[===========TODO EN ORDEN===========]');
        }
    }
}