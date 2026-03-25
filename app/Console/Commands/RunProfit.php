<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\profit\SnconcepModel;
use App\Models\profit\SngenNomiModel;
use App\Models\profit\SnreciboModel;
use App\Models\profit\SnnomiModel;
use App\Models\profit\SnEmpleModel;
use App\Models\recibos\ItemsModels;
use App\Models\recibos\PeriodosModels;
use App\Models\recibos\RecibosNominaModel;
use App\Models\recibos\AsigDeducModel;
use App\Models\personal\NominaModel;
use App\Models\personal\PersonalModel;
use App\Models\recibos\ContNominaModels;
use Carbon\Carbon;

class RunProfit extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RunProfit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carga de Items y Nominas de Empleados';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {     
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {   
        $nominasinf = NominaModel::all();
        $defaultexe = "DB_CONNECTION";    
///1-------------------------recorremos la snominas
        foreach ($nominasinf as $nom) {    
             ///extraemos los items por nomina
            $items = ItemsModels::where('nomina_id',$nom->id)->get()->pluck('coditem');
            
///1-------------------------depende del ID de la nomina conectamos y procesaremos. 
            switch ($nom->id) {                
                   case 1:
                       //DIGA
                       $this->alert("[|-|-|-|- EJECUTANDO NOMINA -  DIGA -|-|-|-|]");
                       $execute = "DB_CONNECTIONDIGA";
                       break;
                   case 2:
                       ///DG
                       $this->alert("[|-|-|-|- EJECUTANDO NOMINA - DG -|-|-|-|]");
                       $execute = "DB_CONNECTIONDG";
                       break;
               }               
            DB::setDefaultConnection(env($execute));
            DB::reconnect();  
///Extramos los items de profit segun la nomina
            $uploads = SnconcepModel::WhereNotIn('co_conce',$items)->get();   
///regresamos a sql local              
            DB::setDefaultConnection(env($defaultexe));
            DB::reconnect();   
            if ($uploads->isNotEmpty()) {  
                foreach ($uploads as $car) {
                    $newitem = new ItemsModels();                   
                    $newitem->coditem       = trim($car->co_conce);
                    $newitem->descripcion   = $car->des_conce;
                    $newitem->tipo          = $car->tipo;
                    $newitem->nomina_id     = $nom->id;
                    $newitem->save();
                }
                $this->info('------> ITEMS NUEVOS PROCESADOS <-------');
            } else {
                $this->info('[====== NO HAY NUEVA INFORMACION ======]');                
            } 
///2---------------extraemos los tipos de recibos que almacenaremos que tengamos activados.
            $tprecibos = ContNominaModels::where('active', 1)->where('nomina_id',$nom->id)->get();            
                       
            ///3---------------recorremos los tipos de nomina LOCALES PARA PROCESAR.            
            
            ///siempre voy a ejecutar los 15 y los 30
                       
            foreach ($tprecibos as $tipodeRecibo) {
              
                switch(trim($tipodeRecibo->codigo)){      
                    case 90: ///UTILIDADES   
                    case "90A":/// ANTICIPO DE UTILIDADES
                            $hoy         = Carbon::now(); 
                            $inicio      = $hoy->copy()->startofYear()->subYear(1); 
                            $fechaActual = $hoy->copy()->endofYear();                            
                        break;
                    case 91: ///VACACIONES  
                            $fechaActual = Carbon::now(); 
                            $inicio      = $fechaActual->copy()->startofYear()->subYear(1);                                                             
                        break;
                    default:/// default para vaciones y nomina normal.
                    $fechaActual    = Carbon::now();  
                    $inicio         = $fechaActual->copy()->startofYear()->subYear(1);                                                                                   
                    break;
                }              
               //dd($fechaActual,$inicio);
                DB::setDefaultConnection(env($defaultexe));
                DB::reconnect();
                ///extraemos todos los id de las nominas que ya hemos cargado            
                $nominasAlmacenadas = PeriodosModels::select('profit_id_table')->where('tiponomina',$nom->id)->where('rp_cont_nomina',$tipodeRecibo->codigo)->get()->ToArray();             
                
                DB::setDefaultConnection(env($execute));
                DB::reconnect();
                                                              
                if (!SngenNomiModel::select('co_gennomi','co_cont', 'des_gennomi', 'fec_ini', 'fec_fin','fec_emis')
                    ->where('co_cont',$tipodeRecibo->codigo)
                    ->WhereNotIn('co_gennomi',$nominasAlmacenadas)  
                    ->whereDate('fec_ini','>=',$inicio)  
                    ->whereDate('fec_emis','<=',$fechaActual)            
                    ->where('estado',2)->exists()) {                                            
                    $this->error('[====== NOMINA NO DISPONIBLE PARA PROCESAR de Tipo :'.$tipodeRecibo->descripcion.' ======]');
                    continue;
                }
                
                $profitnomina = SngenNomiModel::select('co_gennomi','co_cont', 'des_gennomi', 'fec_ini', 'fec_fin','fec_emis')
                    ->where('co_cont',$tipodeRecibo->codigo)
                    ->WhereNotIn('co_gennomi',$nominasAlmacenadas)     
                    ->whereDate('fec_ini','>=',$inicio)     
                    ->whereDate('fec_emis','<=',$fechaActual)            
                    ->where('estado',2)->get();
                
                //// Recorrido de Nominas del profit
                foreach($profitnomina as $profit){                    
                    $this->alert("ALMACENANDO ".$profit->des_gennomi." Con Fecha Final ".date('d/m/Y', strtotime($profit->fec_fin)));
                    $data1 = [$tipodeRecibo, $profit, $nom->id];
                    ////Almacenamos el periodo
                  //  dd($profitnomina);
                    $periodoInfinity = $this->PeriodoCreate($data1, $defaultexe, $execute);  
                    
                    $recibos = SnreciboModel::select('reci_num', 'co_gennomi', 'cod_emp')->where('co_gennomi', $profit->co_gennomi)->get();
                                        
                        foreach ($recibos as $rec) {

                            $empleado = SnEmpleModel::select('ci', 'cod_emp')->where('cod_emp', $rec->cod_emp)->first();
                            ///datos de la persona conjunto al periodo de infinity para crearle la nomina
                            $data2 = [trim($empleado->ci), trim($empleado->cod_emp), $periodoInfinity];

                            ////Generamos el recibo de nomina en infinity
                            $primarynom = $this->NominaCreate($data2, $defaultexe, $execute, $profit);

                            //// validamos los items de asignaciones y deducciones y almacenamos en infinity
                            $proitem = SnnomiModel::select('reci_num', 'reng_num', 'co_conce', 'monto', 'tipo', 'auxi_cha', 'auxi_num')
                            ->where('cod_emp', $data2[1])
                            ->where('reci_num', $rec->reci_num)
                            ->wherein('tipo', [1, 2, 3, 4])->get();
                            ////Generamos las asignaciones y deducciones en infinity
                            $this->AsignaODeducion($primarynom, $proitem, $defaultexe, $execute, $nom->id,trim($empleado->ci));
                        }
                    }
                    
                }
                ///---fin foreach tipo recibo---////            
                DB::setDefaultConnection(env($defaultexe));
                DB::reconnect();
            }
            ///---fin foreach nomina---////            
        }
                 
    ////Registra el periodo, depende del corte en curso
    private function PeriodoCreate($data, $defaultexe, $execute) {
     
        //conectamos a infinity y registramos  $data1 = [$cod, $profit, $nom->id];
        DB::setDefaultConnection(env($defaultexe));
        DB::reconnect(); 
        
        $valueDate      = Carbon::create($data[1]->fec_emis);     /// puede ser 15 o 30 siempre y cuando el typo de nomina recorrida sea quincenal   
        //dd($valueDate,$data[0]->type, $valueDate->copy()->format('F'));
        switch ($data[0]->type){
                case "Q": /// quincenal
                    $inicio         = $valueDate->copy()->startOfMonth();   /// iniciamos a principio de año
                    $final          = $valueDate->copy()->startOfMonth()->add('14 days'); ///la fecha actual, seteamos a principio de mes y sumamos 14 dias                    
                    if ($valueDate <=  $final) {                        
                        $corte      = 1;                        
                    } else {                        
                        $corte      = 2; 
                    }  
                        $mes        = $valueDate->copy()->format('F'); /// mes en palabras
                        $anio       = $valueDate->copy()->format('Y'); ///año que se ejecuta        
                break;                  
                case "A":                   
                    $corte          = 4;  
                    $mes            = $valueDate->copy()->format('F'); /// mes en palabras
                    $anio           = $valueDate->copy()->format('Y'); ///año que se ejecuta        
                break;
                case "M":                   
                    $corte          = 4;           
                    $mes            = $valueDate->copy()->format('F'); /// mes en palabras
                    $anio           = $valueDate->copy()->format('Y'); ///año que se ejecuta        
                break;
                default:
                    $this->line('[============ ABORTADO =============]');
                    return false;
                break;                    
                }
            //    dd($corte);
            $periodo = new PeriodosModels();
            $periodo->profit_id_table   = trim($data[1]->co_gennomi);
            $periodo->corte             = $corte;
            $periodo->mes               = $mes;
            $periodo->anio              = $anio;
            $periodo->tiponomina        = $data[2];
            $periodo->descripcion       = $data[1]->des_gennomi;
            $periodo->rp_cont_nomina    = trim($data[1]->co_cont);
            $periodo->active            = 0;
            $periodo->save();

            $id = $periodo->id;
            
        DB::setDefaultConnection(env($execute));
        DB::reconnect();

        return $id;
    }

    ////registra el la nomina del trabajor depende del periodoCreate
    private function NominaCreate($data, $defaultexe, $execute,$dataprofit) {

        DB::setDefaultConnection(env($defaultexe));
        DB::reconnect();
        str_replace(',', '', $data[0]);
     
        
        $nomina = new RecibosNominaModel();
        $nomina->cedula         = str_replace('.', '',trim($data[0]));
        $nomina->sueldo         = 0;
        $nomina->rp_periodo_id = $data[2];
        
        ///solo aplica la asignacion de la fecha, para los anticipos de utilidades.
        if(trim($dataprofit->co_cont) === "90A"){ 
          $nomina->fec_ini = carbon::create($dataprofit->fec_ini)->format('Y-m-d');
          $nomina->fec_fin = carbon::create($dataprofit->fec_fin)->format('Y-m-d');
        }
        
        $nomina->save();

        if (PersonalModel::where('cedula_empleado', $data[0])->wherenull('cod_profit')->exists()) {
            $pers = PersonalModel::where('cedula_empleado', $data[0])->first();
            $pers->cod_profit = $data[1];
            $pers->save();
        }
        
        DB::setDefaultConnection(env($execute));
        DB::reconnect();
        return $nomina;
    }

    private function AsignaODeducion($newrecibo, $rows, $defaultexe, $execute, $tiponomina,$cedula) {

        DB::setDefaultConnection(env($defaultexe));
        DB::reconnect();
        /// consulta par extraer los codigos que contienen _
       // $items = ItemsModels::where('nomina_id', $tiponomina)->whereRaw('INSTR(coditem, "_") = 0')->pluck('coditem', 'id')->ToArray();
        $cedula = str_replace('.', '',trim($cedula));

        $items = ItemsModels::where('nomina_id', $tiponomina)->pluck('coditem', 'id')->ToArray();
        $montoAsig  = 0;
        $montodeduc = 0;
        $montototal = 0;
        $sueldo     = 0;     
        
        foreach ($rows as $itemsinf) {
       //     dd($itemsinf,array_search(trim($itemsinf->co_conce), $items),$items);
            switch ($itemsinf->tipo) {
                case 1:                 
                    $asignacion = new AsigDeducModel();
                    $asignacion->rp_nomina_id   = $newrecibo->id;
                    $concep                     = trim($itemsinf->co_conce);
                    $asignacion->rp_items_id    = array_search($concep, $items);
                    $asignacion->monto          = $itemsinf->monto;
                    $asignacion->auxi_num      = $itemsinf->auxi_num;
                    $asignacion->auxi_char      = $itemsinf->auxi_cha;
                    $asignacion->save();
                    
                    $montoAsig = $montoAsig + $itemsinf->monto;                    
                    break;
                case 2:
                case 3:
                    $asignacion = new AsigDeducModel();
                    $asignacion->rp_nomina_id = $newrecibo->id;

                    $concep = trim($itemsinf->co_conce);

                    $asignacion->rp_items_id    = array_search($concep, $items);
                    $asignacion->monto          = $itemsinf->monto;
                    $asignacion->auxi_num       = $itemsinf->auxi_num;
                    $asignacion->auxi_char      = $itemsinf->auxi_cha;
                    $asignacion->save();
                    $montodeduc = $montodeduc + $itemsinf->monto;
                    break;
                case 4:                                         
                    switch (trim($itemsinf->co_conce)) {
                        case "Q019_1"; /// suelvo por diario x vacaciones                                                        
                        case "Q024";    /// sueldo mesual   
                      //  dd("aca",trim($itemsinf->co_conce));
                            $sueldo         = $itemsinf->monto;
                            break;
                        case "Y003":                           
                           
                             $newrecibo->fec_ini = date('Y-m-d', strtotime($itemsinf->auxi_cha));     
                            /// fecha de inicio y fin vaciones                        
                            break;
                        case "Y067":                            
                            /// fecha de inicio y fin vaciones  
                            $newrecibo->fec_fin= date('Y-m-d', strtotime($itemsinf->auxi_cha));                     
                            break; 
                    }
                    break;
            }          
           
            if(trim($itemsinf->co_conce) === "A500"){      
                $val = RecibosNominaModel::select('sueldo')
                ->where('cedula',$cedula)->wherenotin('id',[$newrecibo->id])->get()->last();
                $sueldo = ($val !== null ? $val->sueldo : 0 ); //       
            }
            
        }
        
        $montototal = $montoAsig - $montodeduc;

        $newrecibo->monto_total = $montototal;   
        $newrecibo->sueldo      = $sueldo;
        $newrecibo->save();
            
        DB::setDefaultConnection(env($execute));
        DB::reconnect();

        return true;
    }
}