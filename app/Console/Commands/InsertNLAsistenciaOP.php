<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\personal\LibranzasModel;
use App\Models\asistencia\AsistenciaOperativaModel;
use App\Models\personal\CargoModel;
use App\Models\personal\PersonalModel;
use Carbon\Carbon;

class InsertNLAsistenciaOP extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'infinity:InsertNLAsistenciaOP';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registra NL según Ficha de personal por Esquema de Libranza';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $supervisores = CargoModel::where('nombre_cargo', 'like', 'Supervisor%')
                        ->orwhere('nombre_cargo', 'like', 'Analista%')
                        ->orwhere('nombre_cargo', 'like', 'Formador%')->pluck('id');

        $this->supervisores = ["0" => $supervisores, "1" => "supervisor"];

        $ejecutivo = CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
                        ->orwhere('nombre_cargo', 'like', 'Team%')
                        ->orwhere('nombre_cargo', 'like', 'Validador%')
                        ->orwhere('nombre_cargo', 'like', 'Back%')
                        ->orwhere('nombre_cargo', 'like', 'Agente%')->pluck('id');

        $this->ejecutivo = ["0" => $ejecutivo, "1" => "ejecutivos"];

        $coordinador = CargoModel::where('nombre_cargo', 'like', 'Coordinador%')
                        ->orwhere('nombre_cargo', 'like', 'Senior%')->pluck('id');

        $this->coordinador = ["0" => $coordinador, "1" => "coordinador"];

        $senior = CargoModel::Where('nombre_cargo', 'like', 'Senior%')->pluck('id');
        $this->senior = $senior;

        ///14 GSVChile | 21 cuballama | 35 acuba|  57 womchile | 56 renoco | 20 porta concentra
        $this->excluidas = [14, 21, 35, 57, 56, 20];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $fecha = Carbon::now()->format('Y-m-d h:i:s');
        $dia = date('l', strtotime($fecha));
        $diaencurso = trans('semanas.' . $dia);
        $libranza = LibranzasModel::where('libranzas', 'like', '%' . $diaencurso . '%')->pluck('id');
        
        ///Barrida de NL de ejecutivos para en general       
        
        $ob1 = PersonalModel::wherenotIn('cartera_id', $this->excluidas)                      
                ->whereIn('cargo_id', $this->ejecutivo[0])
                ->whereIn('libranza_id', $libranza)
                ->where('estatus_id', 1)->get();
        
        foreach ($ob1 as $personal) {
            if (!AsistenciaOperativaModel::where('personal_id', $personal->id)->where('created_at', $fecha)->where('prefijo', "NL")->exists()) {
                $sistencia = new AsistenciaOperativaModel();
                $sistencia->tipo_asistencia = $this->ejecutivo[1];
                $sistencia->gerente_id = $personal->gerente_area_id;
                $sistencia->supervisor_id = $personal->jefe_inmediato;
                $sistencia->coordinador_id = $personal->jefe_inmediato_segundo;
                $sistencia->personal_id = $personal->id;
                $sistencia->area_trabajo_id = $personal->area_trabajo_id;
                $sistencia->cartera_id = $personal->cartera_id;
                $sistencia->skill_id = $personal->skill_id;
                $sistencia->prefijo = "NL";
                $sistencia->dia = $dia;
                $sistencia->observacion = "N/As";
                $sistencia->created_at = $fecha;
                $sistencia->updated_at = $fecha;
                $sistencia->save();
            }
        }

        ///Barrida de NL de supervisores
        //dd($this->supervisores,$libranza);
        $ob2 = PersonalModel::wherenotIn('cartera_id', $this->excluidas)
                        ->whereIn('cargo_id', $this->supervisores[0])
                        ->whereIn('libranza_id', $libranza)
                        ->where('estatus_id', 1)->get();
        foreach ($ob2 as $personal) {
            if (!AsistenciaOperativaModel::where('personal_id', $personal->id)->where('created_at', $fecha)->where('prefijo', "NL")->exists()) {
                $sistencia = new AsistenciaOperativaModel();
                $sistencia->tipo_asistencia = $this->supervisores[1];
                $sistencia->gerente_id = $personal->gerente_area_id;
                $sistencia->supervisor_id = $personal->jefe_inmediato;
                $sistencia->coordinador_id = $personal->jefe_inmediato_segundo;
                $sistencia->personal_id = $personal->id;
                $sistencia->area_trabajo_id = $personal->area_trabajo_id;
                $sistencia->cartera_id = $personal->cartera_id;
                $sistencia->skill_id = $personal->skill_id;
                $sistencia->prefijo = "NL";
                $sistencia->dia = $dia;
                $sistencia->observacion = "N/A";
                $sistencia->created_at = $fecha;
                $sistencia->updated_at = $fecha;
                $sistencia->save();
            }
        }

        
        ///Barrida de NL de coordinadores y seniors
        $ob3 = PersonalModel::wherenotIn('cartera_id', $this->excluidas)
                        ->whereIn('cargo_id', $this->coordinador[0])->whereIn('libranza_id', $libranza)
                        ->where('estatus_id', 1)->get();
        foreach ($ob3 as $personal) {
            if (!AsistenciaOperativaModel::where('personal_id', $personal->id)->where('created_at', $fecha)->where('prefijo', "NL")->exists()) {
                $sistencia = new AsistenciaOperativaModel();
                $sistencia->tipo_asistencia = $this->coordinador[1];
                $sistencia->gerente_id = $personal->gerente_area_id;
                $sistencia->supervisor_id = $personal->jefe_inmediato;
                $sistencia->coordinador_id = $personal->jefe_inmediato_segundo;
                $sistencia->personal_id = $personal->id;
                $sistencia->area_trabajo_id = $personal->area_trabajo_id;
                $sistencia->cartera_id = $personal->cartera_id;
                $sistencia->skill_id = $personal->skill_id;
                $sistencia->prefijo = "NL";
                $sistencia->dia = $dia;
                $sistencia->observacion = "N/A";
                $sistencia->created_at = $fecha;
                $sistencia->updated_at = $fecha;
                $sistencia->save();
            }
        }

        PersonalModel::whereDate('vacaciones_end','<',$fecha)->where('estatus_id',8)
        ->update(['estatus_id'=>1,
        "vacaciones_init"=>null, 
        "vacaciones_end"=>null]);

        $this->line('CORRIDA DE NL EN ASISTENCIA OPERATIVA PROCESADA');
    }
}
