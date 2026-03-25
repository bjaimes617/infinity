<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;
use Illuminate\Support\Facades\DB;
use App\Models\asistencia\ConvocadosModels;
use App\Models\personal\LibranzasModel;

class ConvocadosAsistenciaRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:convocadoscarteras';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza la corrida de personal activo de las carteras';

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
    public function handle() {
        $this->line('[......REALIZANDO CORRIDA DE CONVOCADOS POR UNIDADES......]');
        
        $fecha = date('Y-m-d h:i:s', strtotime(now()));
        //$fecha = "2023-07-17";
        $dia = date('l', strtotime($fecha));        
        $diaencurso = trans('semanas.' . $dia);
        //dd($diaencurso);
        $libranza = LibranzasModel::where('libranzas','like','%'.$diaencurso.'%')->pluck('id');
       
        $idcargos =  CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
                        ->orwhere('nombre_cargo', 'like', 'Team%')
                        ->orwhere('nombre_cargo', 'like', 'Validador%')
                        ->orwhere('nombre_cargo', 'like', 'Back%')
                ->orwhere('nombre_cargo', 'like', 'Agente%')->pluck('id');

        $convocados = PersonalModel::select(db::raw("count(dir_personal.id) as convocados"), 'dir_personal.cartera_id','skill_id')
                        ->join('dir_skill', 'dir_personal.skill_id', 'dir_skill.id')
                        ->where('dir_skill.active', 1)
                        ->where('dir_skill.cod_ceco', '!=', '00000')
                        ->whereIn('dir_personal.cargo_id', $idcargos)
                        ->whereIn('dir_personal.estatus_id', [1])
                        ->wherenotIn('sede_laboral',["Homeoffice"]) 
                        ->wherenotIn('libranza_id',$libranza) 
                        ->groupBy('skill_id')->get();                
        //dd($convocados,$libranza);
        $begin = date('Y-m-d', strtotime(now()));
        //dd($convocados,$libranza,$idcargos);
        if (!ConvocadosModels::whereBetween('created_at', array("$begin 00:00:01", "$begin 23:59:59"))->exists()) {
            foreach ($convocados as $con) {   
                //35 ACUBA // 21 CUBALLAMA // 14 CLAROCHILE
                if(in_array($con->cartera_id, [35,21,14])){  
                    ///se ejecutara el total de convocados si las carteras estan en el array anterior ya uqe no se tomara la libraza.
                    $exclud = PersonalModel::select(db::raw("count(dir_personal.id) as convocados"), 'skill_id', 'dir_personal.cartera_id')
                        ->join('dir_skill', 'dir_personal.skill_id', 'dir_skill.id')              
                        ->where('dir_personal.cartera_id', $con->cartera_id) 
                        ->where('dir_skill.cod_ceco', '!=', '00000')
                        ->where('dir_skill.active', 1)               
                        ->whereIn('dir_personal.cargo_id', $idcargos)
                        ->whereIn('dir_personal.estatus_id', [1]) 
                        ->wherenotIn('sede_laboral',["Homeoffice"]) 
                        ->groupBy('skill_id')->get();                
                    
                    foreach($exclud as $ex){
                        $rs = new ConvocadosModels();
                        $rs->total_convocados = $ex->convocados;
                        $rs->cartera_id = $ex->cartera_id;
                        $rs->skill_id = $ex->skill_id;
                        $rs->created_at = $fecha;
                        $rs->updated_at = $fecha;
                        $rs->save();
                    }
                    
                } else {                    
                    $rs = new ConvocadosModels();
                    $rs->total_convocados = $con->convocados;
                    $rs->cartera_id = $con->cartera_id;
                    $rs->skill_id = $con->skill_id;
                    $rs->created_at = $fecha;
                    $rs->updated_at = $fecha;
                    $rs->save();
                }
            }
            $this->line('CORRIDA DE CONVOCADOS POR UNIDADES TERMINADA');
        } else {
            $this->line('LA CORRIDA DE CONVOCADOS YA HA SIDO CORRIDA PROCESADA HOY');
        }
    }
}
