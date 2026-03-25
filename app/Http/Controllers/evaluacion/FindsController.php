<?php

namespace App\Http\Controllers\evaluacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\personal\CargoModel;
use App\Models\personal\PersonalModel;
use App\Models\evaluacion\SegmentoModel;
use App\Http\Controllers\PoliceEstructure;
use Carbon\carbon;

class FindsController extends Controller {

    public function __construct() {

        $this->supervisores = CargoModel::where('nombre_cargo', 'like', 'Supervisor%')
                        ->orwhere('nombre_cargo', 'like', 'Analista%')->pluck('id');

        $this->ejecutivo = CargoModel::where('nombre_cargo', 'like', 'Ejecutivo%')
                        ->orwhere('nombre_cargo', 'like', 'Team%')
                        ->orwhere('nombre_cargo', 'like', 'Validador%')
                        ->orwhere('nombre_cargo', 'like', 'Back%')->pluck('id');

        $this->coordinador = CargoModel::where('nombre_cargo', 'like', 'Coordinador%')->pluck('id');

        $this->senior = CargoModel::Where('nombre_cargo', 'like', 'Senior%')->pluck('id');
    }

    ///esto para crear evaluaciones administracion - generar
    public function SearchCargos(Request $request) {

        $exccluir = CargoModel::where('nombre_cargo', 'like', 'Gerente%')
                ->orwhere('nombre_cargo', 'like', 'Gerente%')
                ->orwhere('nombre_cargo', 'like', 'Especialista%')
                ->orwhere('nombre_cargo', 'like', 'Programador%')
                ->orwhere('nombre_cargo', 'like', 'Recepcionista%')
                ->orwhere('nombre_cargo', 'like', 'Psicologo%')
                ->orwhere('nombre_cargo', 'like', 'Pasante%')
                ->orwhere('nombre_cargo', 'like', 'Mentor%')
                ->orwhere('nombre_cargo', 'like', 'Lider%')
                ->orwhere('nombre_cargo', 'like', 'Recepcionista%')
                ->orwhere('nombre_cargo', 'like', 'Asesor%')
                ->where('estatus_id', 1)
                ->pluck('id');

        $segmentos = SegmentoModel::where('area_trabajo_id', $request->area)
                        ->where('cartera_id', $request->cartera)
                        ->where('skill_id', $request->skill)->pluck('cargo_id');

        $idcargos = PersonalModel::where('area_trabajo_id', $request->area)
                ->where('cartera_id', $request->cartera)
                ->where('skill_id', $request->skill)
                ->groupBy('cargo_id')
                ->pluck('cargo_id');

        $cargo = CargoModel::whereIn('id', $idcargos)->Wherenotin('id', $exccluir)->get();

        return json_encode($cargo);
    }

    ///cargos al crear evaluacion
    public function CargosPermisos(Request $request) {

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        $cargos = PoliceEstructure::CargosForEstructure($user, $nivel, $request->skill);

        return json_encode($cargos);
    }

    ///Cambia el selector de Fecha de evalaucion - crear evaluacion mediante cargo
    public function getPeriodo(Request $request) {

        $semanasArray = array();
        $tipo_periodo = null;
        if (SegmentoModel::select('tipo_periodo')->where('cargo_id', $request->cargo_eval)->where('skill_id', $request->skill_asig)->exists()) {

            $tipo_periodo_sql = SegmentoModel::select('tipo_periodo')->where('cargo_id', $request->cargo_eval)->where('skill_id', $request->skill_asig)->first();
            $tipo_periodo = strtolower($tipo_periodo_sql->tipo_periodo);
        }

        if (strtolower($tipo_periodo) == "semanal") {
            
            $fechaActual = Carbon::createfromFormat('m/Y', $request->mes);
            $currentWeek = $fechaActual->copy()->weekOfMonth;
            Switch ($currentWeek) {
                case 1:
                    $minDate = $fechaActual->copy()->subMonth()->format('m/Y');
                    $maxDate = $fechaActual->copy()->format('m/Y');
                    $type_semana = 1;
                    break;
                default :
                    $minDate = $fechaActual->copy()->subMonth()->format('m/Y');
                    $maxDate = $fechaActual->copy()->format('m/Y');
                    $type_semana = 2;
                    break;
            }
            $year = $fechaActual->copy()->format('Y');
            $month = $fechaActual->copy()->format('m');

            $suff = array('st', 'nd', 'rd', 'th', 'th', 'th');   //week suffixes
            $end = date('t', mktime(0, 0, 0, $month, 1, $year));   //last date day of month: 28 - 31
            $start = date('w', mktime(0, 0, 0, $month, 1, $year));  //1st day of month: 0 - 6 (Sun - Sat)
            $last = 8 - $start;      //get last day date (Sat) of first week
            $noweeks = ceil((($end - ($last + 1)) / 7) + 1);  //total no. weeks in month
            $output = [];      //initialize string		
            $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);

            for ($x = 1; $x < $noweeks + 1; $x++) {
                if ($x == 1) {
                    $startdate = "01-$monthlabel-$year";
                    $day = $last - 6;
                } else {
                    $day = $last + 1 + (($x - 2) * 7);
                    $day = str_pad($day, 2, '0', STR_PAD_LEFT);
                    $startdate = "$day-$monthlabel-$year";
                }
                if ($x == $noweeks) {
                    $enddate = "$end-$monthlabel-$year";
                } else {
                    $dayend = $day + 6;
                    $dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
                    $enddate = "$dayend-$monthlabel-$year";
                }
                $output[$x] = "Semana {$x} -> $startdate al $enddate";
            }
            $semanasArray = $output;
        }
        ///completa las 5 semanas 

        return json_encode([$tipo_periodo, $semanasArray]);
    }
}
