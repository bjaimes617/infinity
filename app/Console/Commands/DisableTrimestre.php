<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ari\UnidadTributariaModel;
use Carbon\Carbon;

class DisableTrimestre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:disabletrimestre';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desactiva el trimestre del ARI';

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

        //SACAMOS EL AÑO Y EL TRIMESTRE ACTUAL
       $fechaActual = Carbon::now();
        $anio = $fechaActual->format('Y');

        $now =$fechaActual;

        $trimestre_con = ceil($now->quarter);

        $trimestre = (int)$trimestre_con;

       //SI EL TRIMESTRE ACTUAL ES 1 COMPROBAMOS EL DEL AÑO PASADO SI NO RESTAMOS UNO PARA COMPROBAR EL ANTERIOR

         if ($trimestre == 1) {
              
             $trimestre = 4;

             $anio = $anio - 1;

         }else{

            $trimestre = $trimestre - 1;
        }

        // Calculamos la fecha final del trimestre

         $month = ($trimestre * 3); // El mes final del trimestre

         $fechaFinal = Carbon::createFromDate($anio, $month, 1)->endOfMonth();

        $anio_actual =$fechaActual->format('Y');

        $now_act = $fechaActual;
        $trimestre_act = ceil($now_act->quarter);

        $trimestre_actual = (int)$trimestre_act;

          if(UnidadTributariaModel::where('anio', $anio_actual)->where('trimestre',$trimestre_actual)->exists()) {

              if(UnidadTributariaModel::where('anio', $anio)->where('trimestre',$trimestre)->exists()){

         $tri_sql = UnidadTributariaModel::where('anio',$anio)->where('trimestre',$trimestre)->first();

         //SI LA FECHA FINAL DEL TRIMESTRE ANTERIOR ES MENOR A LA FECHA ACTUAL DESACTIVAMOS EL TRIMESTRE
       // dd($fechaFinal < $fechaActual, $fechaFinal, $fechaActual, $tri_sql);
         if ($fechaFinal < $fechaActual) {

             $tri_sql->disabled = 1;
             $tri_sql->active = 0;

             $this->line('TRIMESTRE ANTERIOR DESACTIVADO EXITOSAMENTE');

         }else{

            $tri_sql->disabled = 0;
            $tri_sql->active = 1;

             $this->line('EL TRIMESTRE NO HA PODIDO DESACTIVARSE');

         }

         $tri_sql->save();

     }else{
          
          $this->line('EL PERIODO ANTERIOR NO ESTA ACTIVO O NO EXISTE');

     }

    }else{

         $this->line('EL PERIODO ACTUAL NO ESTA ACTIVO O NO EXISTE');
    }
}
}
