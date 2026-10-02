<?php

namespace App\Http\Controllers\comisiones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\PoliceEstructure;

use App\Imports\comisiones\ComisionesQuincenalesImport;
use App\Imports\comisiones\ComisionesSemanalesImport;
use App\Imports\comisiones\ObservacionesImport;

use App\Models\personal\BancosModels;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\SkillRangoQuincenal;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;

use App\Models\Comisiones\OperacionesComisiones;
use App\Models\Comisiones\LoteModel;
use App\Models\Comisiones\BancoModel;
use App\Models\Comisiones\LoteBancoModel;

use App\Exports\Comisiones\BancoExport;
use App\Exports\Comisiones\LoteBancoExport;
use App\Exports\Comisiones\ObservacionesExport;

use App\Exports\Comisiones\TrabajadorExport;
use App\Exports\Comisiones\TrabajadorSemanalExport;

use App\Exports\Comisiones\AuditoriaExport;
use App\Exports\Comisiones\AuditoriaSemanalExport;
use App\Exports\Comisiones\AuditoriaBaitExport;

use App\Exports\Comisiones\ComisionesGeneralExport;

use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ComisionesController extends Controller
{

    //*incidencia 3 -> auditado
    //incidencia 2 -> no procesado
    //incidencia 1 -> incidencia de pago
    //incidencia 0 -> no problemas*//
    public function __construct()
    {
        $this->esquema = ["Quincenal" => "Quincenal", "Semanal" => "Semanal"];

        $this->statusAuditoria = [
            "NO PROCESADO" => "No Procesado",
            "AUDITADO" => "Auditado",
            "PAGADO" => "Pagado"
        ];
        //,"PAGADO CON INCIDENCIAS"=>"Pagado con Incidencias"];

        $this->estatusbancarios = [
            "todos" => "[TODOS]",
            "EN PROCESO" => "En Proceso",
            "PAGADO" => "Pagado",
            "PAGADO CON INCIDENCIAS" => "Pagado con Incidencias"
        ];

        $this->Numero_meses_anteriores_permitido    = 6;
        $this->Numero_meses_anteriores_no_permitido = 1;
    }

    public function index()
    {

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        $mes        = $user->hasPermission('comisiones.cargar.demoradas') == true ?  $this->Numero_meses_anteriores_permitido : $this->Numero_meses_anteriores_no_permitido;

        $permiso    = $user->hasPermission('comisiones.cargar.demoradas') == true  ? "true" : "false";

        return view('comisiones.index')->with(["areas" => $areas, "esquema" => $this->esquema, "timeMes" => $mes, "permiso" => $permiso]);
    }

    private function ValidateEstructureRangoFechas($intMeses, $tipo_periodo, $fechaActual, $request)
    {

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();
        // Usamos el día REAL de hoy para validar autorización,
        // independientemente del mes seleccionado en el selector.
        $actual = Carbon::now();
        switch ($tipo_periodo) {
            case "Semanal":
                if ((isset($request->skill) || $user->hasPermission('comisiones.cargar.demoradas'))) {
                    $skill = SkillModels::find($request->skill);
                    $autorizados = json_decode($skill->dias);
                    $diaActual = $actual->copy()->dayName;
                    if ($autorizados === null || array_search($diaActual, $autorizados, true) === false) {
                        return json_encode(abort(500, "El Skill no esta autorizado a cargar Hoy " . $diaActual . " o no Esta habilitado para cargar."));
                    }
                }
                //DETERMINAMOS EN QUE SEMANA ESTAMOS DEL MES EN CURSO
                $semanaencurso = $actual->copy()->weekOfMonth;
                switch ($semanaencurso) {
                    case 1:
                        $minDate = $fechaActual->copy()->subMonth($intMeses)->format('m/Y');
                        $maxDate = $fechaActual->copy()->format('m/Y');
                        $type_semana = 1;
                        break;
                    default:
                        $minDate = $fechaActual->copy()->subMonth($intMeses)->format('m/Y');
                        $maxDate = $fechaActual->copy()->format('m/Y');
                        $type_semana = 2;
                        break;
                }
                $year   = $fechaActual->copy()->format('Y');
                $month  = $fechaActual->copy()->format('m');

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

                return [$minDate, $maxDate, null, null, $semanasArray, $semanaencurso, $type_semana];

                break;
            case "Quincenal": //SI EL TIPO DE PERIODO QUE VIENE ES QUINCENAL
                $rangos = SkillRangoQuincenal::where('skill_id', $request->skill)->first();
                $diaDelMes = $fechaActual->day; // Obtiene el día del mes  

                if (($rangos->desde1 == $rangos->desde2 && $rangos->hasta1 == $rangos->hasta2) || $user->hasPermission('comisiones.cargar.demoradas') == true) {
                    if ($diaDelMes >= $rangos->desde1 && $diaDelMes <= $rangos->hasta1 || $user->hasPermission('comisiones.cargar.demoradas') == true) {
                        //mostrara ambos cortes
                        $quicena_en_curso = 3; // Primera quincena
                        $minDate = $fechaActual->copy()->subMonth($intMeses)->format('m/Y');
                        $maxDate = $fechaActual->copy()->format('m/Y');

                        $opcion_corte_show = ["1" => "1ra. Quincena", "2" => "2da. Quincena"];

                        return [$minDate, $maxDate, $opcion_corte_show, $quicena_en_curso, null, null, null];
                    } else {
                        return json_encode(abort(500, "No se encuentra dentro del rango permitido de carga"));
                    }
                } else if ($diaDelMes >= $rangos->desde1 && $diaDelMes <= $rangos->hasta1) {
                    $quicena_en_curso = 2; // Segunda quincena
                    //DE LO CONTRARIO MOSTRAMOS SOLO EL MES ACTUAL
                    $minDate = $fechaActual->copy()->subMonth($intMeses)->format('m/Y');
                    $maxDate = Carbon::now()->format('m/Y');
                    //MOSTRAMOS PRIMERA QUINCENA EN EL SELECTOR DE CORTES si el mes en curso es igual al mes recibido
                    //entonces solo mostramos un corte, en caso de que el mes sea diferente al en curso mostramos ambas opciones
                    if ($maxDate === $fechaActual->copy()->format('m/Y')) {
                        $opcion_corte_show = ["1" => "1ra. Quincena"];
                    } else {
                        $opcion_corte_show = ["1" => "1ra. Quincena", "2" => "2da. Quincena"];
                    }
                    return [$minDate, $maxDate, $opcion_corte_show, $quicena_en_curso, null, null, null];
                } else if (($diaDelMes >= $rangos->desde2 && $diaDelMes <= $rangos->hasta2) || $user->hasPermission('comisiones.cargar.demoradas') == true) {

                    $quicena_en_curso = 1; // Primera quincena
                    $minDate = $fechaActual->copy()->subMonth($intMeses)->format('m/Y');
                    $maxDate = Carbon::now()->format('m/Y');
                    if ($maxDate === $fechaActual->copy()->format('m/Y')) {
                        $opcion_corte_show = ["2" => "2da. Quincena"];
                    } else {
                        $opcion_corte_show = ["1" => "1ra. Quincena", "2" => "2da. Quincena"];
                    }

                    return [$minDate, $maxDate, $opcion_corte_show, $quicena_en_curso, null, null, null];
                } else {
                    return json_encode(abort(500, "No se encuentra dentro del rango permitido de carga"));
                }
                break;
            case "Mixto":
                $maxDate = $fechaActual->copy()->format('m/Y');
                return [null, $maxDate, null, null, null, null, null];
                break;
            default:
                return json_encode(abort(500, "El Skill seleccionado No cuenta con el esquema de pago."));
                break;
        }
    }
    ///funcion para Devolver el esquema de pagos ----> quincenal, mensual o mixto
    public function getTipoPago(Request $request)
    {

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        if (SkillModels::select('tipo_periodo')->where('id', $request->skill)->exists()) {
            $tipo_periodo_sql = SkillModels::select('tipo_periodo', 'type_format_uploads')->where('id', $request->skill)->first();
            $tipo_periodo = $tipo_periodo_sql->tipo_periodo;
            $tipo_formatos = json_decode($tipo_periodo_sql->type_format_uploads);
        } else {
            $tipo_periodo = "";
            $tipo_formatos = "";
        }
        ///Basados en la Fecha en curso se mostrara las primeras opciones
        $fechaActual = Carbon::now();
        //validacion de permisos para cargas demoradas se mostrara 1 si no tiene permisos, y 6 si los tiene
        $intMeses = !$user->hasPermission('comisiones.cargar.demoradas') == true ?
            $this->Numero_meses_anteriores_permitido :
            $this->Numero_meses_anteriores_no_permitido;

        $rangoFechas = $this->ValidateEstructureRangoFechas($intMeses, $tipo_periodo, $fechaActual, $request);
        /* [
            0 => minDate,
            1 => maxDate,
            2 => opcion_corte_show,
            3 => quicena_en_curso
            4 => semanasArray
            5 => semanaencurso
        ] */
        $quicena_en_curso = $rangoFechas[3];
        $minDate = $rangoFechas[0];
        $maxDate = $rangoFechas[1];
        $opcion_corte_show = $rangoFechas[2];
        $semanasArray = $rangoFechas[4];
        $semanaencurso = $rangoFechas[5];
        $type_semana = $rangoFechas[6];

        return json_encode([
            'tipo_periodo'      => $tipo_periodo,
            'tipo_formatos'     => $tipo_formatos,
            'quincena_en_curso' => $quicena_en_curso ?? null,
            'minDate'           => $minDate  ?? null,
            'maxDate'           => $maxDate  ?? null,
            'opcion_corte_show' => $opcion_corte_show ?? null,
            'arreglo_semanal'   => $semanasArray ?? null,
            'semana_en_curso'   => $semanaencurso ?? null
        ]);
    }

    ///funcion para Devolver el esquema de pagos ----> cuando el esquema es Mixto
    public function getPreviousAndCurrentWeek(Request $request)
    {

        $tipo_periodo = $request->tipo_periodo;

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        $fechaActual = Carbon::now();
        //validacion de permisos para cargas demoradas se mostrara 1 si no tiene permisos, y 6 si los tiene
        $intMeses = $user->hasPermission('comisiones.cargar.demoradas') == true ?  $this->Numero_meses_anteriores_permitido : $this->Numero_meses_anteriores_no_permitido;

        $rangoFechas = $this->ValidateEstructureRangoFechas($intMeses, $tipo_periodo, $fechaActual, $request);
        /* [
            0 => minDate,
            1 => maxDate,
            2 => opcion_corte_show,
            3 => quicena_en_curso
            4 => semanasArray
            5 => semanaencurso
        ] */
        $quicena_en_curso = $rangoFechas[3];
        $minDate = $rangoFechas[0];
        $maxDate = $rangoFechas[1];
        $opcion_corte_show = $rangoFechas[2];
        $semanasArray = $rangoFechas[4];
        $semanaencurso = $rangoFechas[5];
        $type_semana = $rangoFechas[6];

        return json_encode([
            'tipo_periodo'      => $tipo_periodo,
            'quincena_en_curso' => $quicena_en_curso ?? null,
            'minDate'           => $minDate  ?? null,
            'maxDate'           => $maxDate  ?? null,
            'opcion_corte_show' => $opcion_corte_show ?? null,
            'arreglo_semanal'   => $semanasArray ?? null,
            'semana_en_curso'   => $semanaencurso ?? null
        ]);
    }

    ////recarga los tipos de cortes si el mes cambia
    public function getCorte(Request $request)
    {

        /// $fecharecibida = Carbon::createfromFormat('m/Y', $request->mes);
        $fechaActual = isset($request->mes) ? Carbon::createfromFormat('m/Y', $request->mes)->startOfMonth() : Carbon::now();
        $fechaActual = $fechaActual->addWeekday();
        $tipo_periodo = isset($request->tipo_periodo) ? $request->tipo_periodo : SkillModels::find($request->skill)->tipo_periodo;
        //    dd($fechaActual);
        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        //validacion de permisos para cargas demoradas se mostrara 1 si no tiene permisos, y 6 si los tiene
        $intMeses = $user->hasPermission('comisiones.cargar.demoradas') == true ?  $this->Numero_meses_anteriores_permitido : $this->Numero_meses_anteriores_no_permitido;

        $rangoFechas = $this->ValidateEstructureRangoFechas($intMeses, $tipo_periodo, $fechaActual, $request);
        /* [
            0 => minDate,
            1 => maxDate,
            2 => opcion_corte_show,
            3 => quicena_en_curso
            4 => semanasArray
            5 => semanaencurso
        ] */
        $quicena_en_curso = $rangoFechas[3];
        $minDate = $rangoFechas[0];
        $maxDate = $rangoFechas[1];
        $opcion_corte_show = $rangoFechas[2];
        $semanasArray = $rangoFechas[4];
        $semanaencurso = $rangoFechas[5];
        $type_semana = $rangoFechas[6];

        return json_encode([
            'tipo_periodo'      => $tipo_periodo,
            'quincena_en_curso' => $quicena_en_curso ?? null,
            'minDate'           => $minDate  ?? null,
            'maxDate'           => $maxDate  ?? null,
            'opcion_corte_show' => $opcion_corte_show ?? null,
            'arreglo_semanal'   => $semanasArray ?? null,
            'semana_en_curso'   => $semanaencurso ?? null
        ]);
    }

    ///procesamiento de los XLS
    public function store(Request $request)
    {
        // Cargar el archivo Excel      
        $spreadsheet = IOFactory::load($request->file('archivo')->getRealPath());
        // Obtener la primera hoja de cálculo
        $worksheet = $spreadsheet->getActiveSheet();
        // Contar el número de filas (registros)
        $totalRows = $worksheet->getHighestRow(); // Esto devuelve el número total de filas

        $headings = (new HeadingRowImport)->toArray($request->file('archivo'));
        if (in_array("altas", $headings[0][0])) {
            $typeImport = "B";
        } else {
            $typeImport = "A";
        }

        $file = $request->file('archivo');
        $nombreArchivo = $file->getClientOriginalName();

        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->mes)->startOfMonth();
        $mes = $fechaCarbon->copy()->format('F');

        $anio = $fechaCarbon->copy()->format('Y'); // Año seleccionado

        $habilidad = SkillModels::where('id', $request->skill)->first();
        $formato_autorizado = json_decode($habilidad->type_format_uploads, true);
        if (!in_array($typeImport, $formato_autorizado)) {
            return back()->with('error', "El formato del archivo no es el correcto para este Skill.");
        }

        $nombre_habilidad = $habilidad->nombre_skill;

        $tipoperiodo = $habilidad->tipo_periodo;

        switch ($habilidad->tipo_periodo) {
            case "Quincenal":
                switch ($request->periodo) {
                    case 1:
                        $periodo = "1era Quincena";
                        break;
                    case 2:
                        $periodo = "2da Quincena";
                        break;
                }
                break;
            case "Semanal":
                switch ($request->periodo) {
                    case 1:
                        $periodo = "Semana 1";
                        break;
                    case 2:
                        $periodo = "Semana 2";
                        break;
                    case 3:
                        $periodo = "Semana 3";
                        break;
                    case 4:
                        $periodo = "Semana 4";
                        break;
                    case 5:
                        $periodo = "Semana 5";
                        break;
                }
                break;
            case "Mixto":
                $tipoperiodo = $request->tipo_periodo;
                if ($request->tipo_periodo == "Quincenal") {
                    switch ($request->periodo) {
                        case 1:
                            $periodo = "1era Quincena";
                            break;
                        case 2:
                            $periodo = "2da Quincena";
                            break;
                    }
                } else {
                    switch ($request->periodo) {
                        case 1:
                            $periodo = "Semana 1";
                            break;
                        case 2:
                            $periodo = "Semana 2";
                            break;
                        case 3:
                            $periodo = "Semana 3";
                            break;
                        case 4:
                            $periodo = "Semana 4";
                            break;
                        case 5:
                            $periodo = "Semana 5";
                            break;
                    }
                }
                break;
        }
        //GUARDAMOS EL NUEVO LOTE
        $lote = new LoteModel();
        $lote->nombre_lote = null;
        $lote->gerencia_id = $request->area_trabajo;
        $lote->campana_id = $request->carteras;
        $lote->skill_id = $request->skill;
        $lote->tipo_periodo = $tipoperiodo;
        $lote->periodo = $request->periodo;
        $lote->anio = $anio;
        $lote->mes  = $mes;
        $lote->estatus = "NO PROCESADO";
        $lote->lotebanco_id = null;
        $lote->formato = $typeImport;

        //COLOCAMOS UN IDENTIFICADOR AL ARCHIVO Y LUEGO LO ALMACENAMOS EN EL SERVIDOR 
        $gerencia = AreatrabajoModel::select('nombre_campana', 'abrev')->where('id', $request->area_trabajo)->first();
        $nombre_gerencia = $gerencia->nombre_campana;
        $abrev = $gerencia->abrev;

        $campana = CarteraModel::select('nombre_cartera')->where('id', $request->carteras)->first();
        $nombre_campana = $campana->nombre_cartera;

        $mes_anio = $fechaCarbon->copy()->format('m_Y');

        $nombre_archivo = date('d_m_Y H_i_s') . '_' . $nombre_gerencia . '_' . $nombre_campana . '_' . $nombre_habilidad . '_' . $tipoperiodo . '_' . $periodo . '_' . $mes_anio . '_' . $file->getClientOriginalName();
        $nombre_archivo_bd = strtoupper($abrev . $nombre_campana . " " . $nombre_habilidad . " " . $periodo . " " . $mes_anio . "_" . $totalRows);
        $fileName = str_replace(' ', '_', $nombre_archivo);
        $lote->nombre_lote = $nombre_archivo_bd;
        $lote->save();

        $path = Storage::disk('comisiones')->put($fileName, file_get_contents($file->getRealPath()));
        $valores = array();
        //SACAMOS EL ID DEL ULTIMO LOTE Y LO MANDAMOS AL IMPORT CON LOS DEMAS DATOS
        $valores = [
            $request->area_trabajo,
            $request->carteras,
            $request->skill,
            $tipoperiodo,
            $request->periodo,
            $fechaCarbon->copy()->format('m'),
            $fechaCarbon->copy()->format('Y'),
            $lote->id,
            $fileName
        ];

        //SI ES PORTABILIDAD CONCENTRA COMPROBAMOS LOS HEADINGS DEL ARCHIVO SELECCIONADO
        try {
            if ($typeImport == "A") { //quincenal
                Excel::import(new ComisionesQuincenalesImport($valores), request()->file('archivo'));
            } else { //semanal                       
                Excel::import(new ComisionesSemanalesImport($valores), request()->file('archivo'));
            }
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) { //retorno de las reglas de validacion
            if (LoteModel::where('id', $lote->id)->exists()) {
                LoteModel::where('id', $lote->id)->first()->delete();
            }
            Storage::disk('comisiones')->delete($fileName);
            return back()->withErrors($e->failures());
        } catch (\App\Exceptions\ValidateAditionals $e) { //catch de validaciones adicionales del archivo
            if (LoteModel::where('id', $lote->id)->exists()) {
                LoteModel::where('id', $lote->id)->first()->delete();
            }
            Storage::disk('comisiones')->delete($fileName);
            // Errores de tus validaciones personalizadas
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $t) { //errornes comunes
            if (LoteModel::where('id', $lote->id)->exists()) {
                LoteModel::where('id', $lote->id)->first()->delete();
            }
            Storage::disk('comisiones')->delete($fileName);
            $mensaje = str_replace("\n", 'and', $t->getMessage());
            \Session::flash('error', "Error: " . $mensaje !== null ? $mensaje : $t->getMessage());
            return back();
        }
    }

    //// proceso de auditoria, ver, eliminar o revertir estatus.
    public function auditoria()
    {
        return view('comisiones.auditoria')->with(["auditorias" => $this->statusAuditoria, "tipo_periodo" => $this->esquema]);
    }

    public function auditoriaList(Request $request)
    {

        $lotes = array();

        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->auditmes)->startOfMonth();
        $anio = $fechaCarbon->copy()->format('Y'); // Año seleccionado
        $mes = $fechaCarbon->copy()->format('F');

        //MANDAMOS LOS LOTES CON EL ESTATUS SELECCIONADO A LA VISTA
        $sql = LoteModel::where('anio', $anio)->where('mes', $mes)->where('estatus', $request->estatus);

        if (!empty($request->tipoperiodo)) {
            $lotes = $sql->where('tipo_periodo', $request->tipoperiodo)->get();
        } else {
            $lotes = $sql->get();
        }

        ///bloqueamos el restaurar solo habilitarlos si esta auditado

        $block = $request->estatus == "NO PROCESADO" ? "disabled" : "";
        $display = $request->estatus == "PAGADO" ? true : false; //si esta pagado, ocultamos el reverse y delete
        $row = array();
        $data = array();

        foreach ($lotes as $lots) {
            $row["id"] = $lots->id;
            $row["check"] = '<div class="custom-control custom-checkbox">
            <input class="custom-control-input check" name="comisiones[]" type="checkbox" id="comisiones' . $lots->id . '" value="' . $lots->id . '">
            <label for="comisiones' . $lots->id . '" class="custom-control-label"></label></div>';
            $row["nombre"] = $lots->nombre_lote;
            $row["gerencia"] = $lots->relationGerencia->nombre_campana;
            $row["campana"] = $lots->relationCampana->nombre_cartera;
            $row["skill"] = $lots->relationSkill->nombre_skill;
            $row["tipo_periodo"] = $lots->tipo_periodo;
            $row["fecha"] = trans('meses.' . $lots->mes) . " " . $lots->anio;

            $btnview = '<a type="button" id="view' . $lots->id . '" href="' . route('com.auditoria.view', $lots->id) . '"                      
                class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Lote">
                <i class="fa fa-download"></i></a>';

            $acciontext = "'delete'";
            $btndelete = '<button type="button" id="delete' . $lots->id . '" data-href="' . route('lote.destroy', $lots->id) . '" onclick="AccionesAudit(' . $acciontext . ',' . $lots->id . ');" 
                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar Lote">
                        <i class="fa fa-times"></i>
                    </button>';

            $acciontext = "'restore'";
            $btnrestore = '<button type="button" ' . $block . ' id="restore' . $lots->id . '" data-href="' . route('lote.reverse', $lots->id) . '" onclick="AccionesAudit(' . $acciontext . ',' . $lots->id . ');" 
                        class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Revertir estatus del Lote">
                       <i class="fa fa-undo"></i>
                    </button>';

            if ($display) {
                /// true para ocultar todos los botones cuando esta pagado
                $btngroup = '<div class="btn-group">' . $btnview . '</div>';
            } else {
                $btngroup = '<div class="btn-group">' . $btnview . $btnrestore . $btndelete . '</div>';
            }


            $row["acciones"] = $btngroup;

            $data[] = $row;
        }

        return json_encode([$display, $data]);
    }

    public function auditoriaView($lote)
    {

        $lote_id = $lote;
        $formato_sql = LoteModel::where('id', $lote_id)->first();
        $auditoria = OperacionesComisiones::where('lote_id', $lote_id)->get();

        $nombre_auditoria = $formato_sql->relationGerencia->nombre_campana .
            " " . $formato_sql->relationCampana->nombre_cartera .
            " " . $formato_sql->relationSkill->nombre_skill .
            " " . $formato_sql->tipo_periodo .
            " " . trans('meses.' . $formato_sql->mes) .
            " " . $formato_sql->anio;

        $formato = $formato_sql->formato;
        //SI NO ES BAIT COMPROBAMOS EL FORMATO PROCESADO
        if ($formato_sql->campana_id != 61) {
            if ($formato == "A") {
                return Excel::download(new AuditoriaExport($auditoria), 'Auditoria ' . $nombre_auditoria . '.xlsx');
            } else {
                return Excel::download(new AuditoriaSemanalExport($auditoria), 'Auditoria ' . $nombre_auditoria . '.xlsx');
            }
        } else {
            //SI ES BAIT LLAMAMOS SU EXPORT RESPECTIVO
            return Excel::download(new AuditoriaBaitExport($auditoria), 'Auditoria ' . $nombre_auditoria . '.xlsx');
        }
    }

    public function loteDestroy($id)
    {

        OperacionesComisiones::where('lote_id', $id)->delete();
        LoteModel::where('id', $id)->delete();

        return json_encode("Lote de datos eliminado exitosamente.");
    }

    public function loteReverse($id)
    {

        OperacionesComisiones::where('lote_id', $id)->update(['incidencia' => 2]);
        LoteModel::where('id', $id)->update(['estatus' => 'NO PROCESADO']);

        return json_encode("Estatus del Lote Reversado a No Procesado");
    }

    ///aprobar los archivos cargados para consolidar
    public function auditoriaProcess(Request $request)
    {

        try {
            //RECORREMOS TODOS LOS LOTES SELECCIONADOS POR ID
            foreach ($request->comisiones as $id) {
                //ACTUALIZAMOS EL ESTATUS DE LOS LOTES A AUDITADOS
                $estatus_lote = LoteModel::where('id', $id)->first();
                $estatus_lote->estatus = "AUDITADO";
                $estatus_lote->save();

                //ESTATUS 3 SIGNIFICA AUDITADO
                OperacionesComisiones::where('lote_id', $id)->update(['incidencia' => 3]);
            }
            \Session::flash('success', "Lote de pagos Seleccionados auditados Exitosamente.");
            return back();
        } catch (\Exception $e) {
            \Session::flash('error', "Error auditando pagos, " . $e->getMessage());
            return back();
        }
    }

    ////procesar pagos, se arma el consolidado Bancario
    public function payment()
    {
        return view('comisiones.payment')->with(["tipo_periodo" => $this->esquema]);
    }

    public function paymentList(Request $request)
    {

        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->paymentmes)->startOfMonth();
        $anio = $fechaCarbon->copy()->format('Y'); // Año seleccionado
        $mes = $fechaCarbon->copy()->format('F');

        //MANDAMOS LOS LOTES CON EL ESTATUS SELECCIONADO A LA VISTA
        $lotes = LoteModel::where('anio', $anio)->where('mes', $mes)
            ->where('tipo_periodo', $request->tipoperiodo)->where('estatus', $request->estatus)->get();

        ///bloqueamos el restaurar solo habilitarlos si esta auditado
        $display = empty($lotes) ? true : false;
        $row = array();
        $data = array();

        foreach ($lotes as $lots) {
            $row["id"] = $lots->id;
            $row["check"] = '<div class="custom-control custom-checkbox">
            <input class="custom-control-input check" name="comisiones[]" type="checkbox" id="comisiones' . $lots->id . '" value="' . $lots->id . '">
            <label for="comisiones' . $lots->id . '" class="custom-control-label"></label></div>';
            $row["nombre"] = $lots->nombre_lote;
            $row["gerencia"] = $lots->relationGerencia->nombre_campana;
            $row["campana"] = $lots->relationCampana->nombre_cartera;
            $row["skill"] = $lots->relationSkill->nombre_skill;
            $row["tipo_periodo"] = $lots->tipo_periodo;
            $row["fecha"] = trans('meses.' . $lots->mes) . " " . $lots->anio;

            $data[] = $row;
        }

        return json_encode([$display, $data]);
    }

    public function paymentProcess(Request $request)
    {
        //DECLARAMOS ARREGLO MULTIDIMENSIONAL VACIO PARA METER LOS DATOS DE LOS TRABAJADORES
        $trabajadorNuevo = [];
        $bs_tasa = $request->bs_tasa;
        $lote_banco = $request->lote_banco;

        //PRIMERO GUARDAMOS EL LOTE DE BANCO PARA UNIFICAR LOS PAGOS Y SACAR EL ID DEL MISMO

        $com_lote_banco = new LoteBancoModel();
        $com_lote_banco->lote_banco = $lote_banco;
        $com_lote_banco->estatus = "EN PROCESO";
        $com_lote_banco->banco = null;
        $com_lote_banco->fecha_pago = null;
        $com_lote_banco->save();

        $id_loteb = $com_lote_banco->id;

        try {
            //RECORREMOS TODOS LOS LOTES SELECCIONADOS POR ID
            foreach ($request->comisiones as $id) {
                //ASIGNAMOS EL LOTE BANCO ID A LOS LOTES ANTERIORES SELECCIONADOS
                $up_lote = LoteModel::where('id', $id)->first();
                $up_lote->lotebanco_id = $id_loteb;
                $up_lote->save();

                //LUEGO BUSCAMOS TODOS LOS DATOS DEL LOTE PARA PROCESARLOS INDIPENDIENTEMENTE QUE SE REPITAN

                $operaciones = OperacionesComisiones::where('lote_id', $id)->get();
                foreach ($operaciones as $lote) {
                    $search_array = $trabajadorNuevo;
                    $tasa_usd = floatval(str_replace(",", ".", $bs_tasa));
                    //SI EXISTE EL TRABAJADOR EN EL SIGUIENTE LOTE SUMAMOS SU MONTO RESPECTIVO DE LO CONTRARIO INSERTAMOS EL NUEVO TRABAJADOR EN EL ARRAY

                    if (array_key_exists($lote->personal_id, $search_array)) {

                        $lote_total_usd = floatval(str_replace(",", ".", $lote->total_usd));
                        $trabajadorNuevo[$lote->personal_id]['usd'] = $trabajadorNuevo[$lote->personal_id]['usd'] + $lote_total_usd;
                        $trabajadorNuevo[$lote->personal_id]['total_pagar'] = $trabajadorNuevo[$lote->personal_id]['usd'] * $tasa_usd;
                    } else {
                        $lote_total_usd = floatval(str_replace(",", ".", $lote->total_usd));
                        $totales = floatval($lote_total_usd) * floatval($tasa_usd);

                        $trabajadorNuevo[$lote->personal_id] = [
                            'personal_id' => $lote->personal_id,
                            'gerencia_id' => $lote->gerencia_id,
                            'campana_id' => $lote->campana_id,
                            'skill_id' => $lote->skill_id,
                            'anio' => $lote->anio,
                            'mes' => $lote->mes,
                            'cedula' => $lote->cedula,
                            'observaciones' => null,
                            'usd' => $lote_total_usd,
                            'bs_tasa' => $tasa_usd,
                            'total_pagar' => $totales,
                            'lotebanco_id' => $id_loteb
                        ];
                        //dd($trabajadorNuevo);
                    }
                }

                //ACTUALIZAMOS EL ESTATUS DE LOS LOTES 

                $estatus_lote = LoteModel::where('id', $id)->first();
                $estatus_lote->estatus = "EN PROCESO";
                $estatus_lote->save();
            }

            //GUARDAMOS  EL MES Y EL ANIO DEL LOTE BANCO

            $mes_anio_lote = LoteModel::select('anio', 'mes')->where('lotebanco_id', $id_loteb)->first();

            $com_lote_banco->mes = $mes_anio_lote->mes;
            $com_lote_banco->anio = $mes_anio_lote->anio;
            $com_lote_banco->save();

            //INSERTAMOS LOS DATOS EN LA TABLA DEL BANCO

            foreach ($trabajadorNuevo as $key => $comision) {

                $banco = new BancoModel();
                $banco->personal_id = $comision['personal_id'];
                $banco->gerencia_id = $comision['gerencia_id'];
                $banco->campana_id  = $comision['campana_id'];
                $banco->skill_id    = $comision['skill_id'];
                $banco->anio        = $comision['anio'];
                $banco->mes         = $comision['mes'];
                $banco->cedula      = $comision['cedula'];
                $banco->observaciones = null;
                $banco->usd             = $comision['usd'];
                $banco->bs_tasa         = $comision['bs_tasa'];
                $banco->total_pagar     = $comision['total_pagar'];
                $banco->lotebanco_id    = $comision['lotebanco_id'];
                $banco->estatus     = "EN PROCESO";
                $banco->save();
            }

            \Session::flash('success', "Cosolidado Bancario Generado Exitosamente.");
            return back();
        } catch (\Exception $e) {
            \Session::flash('error', "Error procesando pagos, " . $e->getMessage());
            return back();
        }
    }

    public function bank()
    {
        $bancos = BancosModels::whereIn('id', [9, 19])->get();
        $estatus = $this->estatusbancarios;
        return view('comisiones.bancos.index')->with(["bancos" => $bancos, "estatusbancarios" => $estatus])->render();
    }
    ////pestaña pagar
    public function bankShowList(Request $request)
    {

        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->periodo)->startOfMonth();

        $fechaFormateada = $fechaCarbon->copy()->format('d-m-Y');
        $mes = $fechaCarbon->copy()->format('F');
        $anio = $fechaCarbon->copy()->format('Y');

        $lotes = LoteBancoModel::where('anio', $anio)->where('mes', $mes)->where('estatus', 'EN PROCESO')->get();

        //MANDAMOS LOS BANCOS A LA VISTA PARA REALIZAR LOS PAGOS
        $row = array();
        $data = array();
        $display = empty($lotes) ? true : false;
        foreach ($lotes as $lots) {
            $row["id"] = $lots->id;
            $row["check"] = '<div class="custom-control custom-checkbox">
            <input class="custom-control-input check" name="consolidado[]" type="checkbox" id="consolidado' . $lots->id . '" value="' . $lots->id . '">
            <label for="consolidado' . $lots->id . '" class="custom-control-label"></label></div>';
            $row["nombre"] = $lots->lote_banco;
            $row["estatus"] = $lots->estatus;
            $row["fecha"] = trans('meses.' . $lots->mes) . " " . $lots->anio;

            $btnview = '<a type="button" id="view' . $lots->id . '" href="' . route('comisiones.bank.export', $lots->id) . '"                      
                class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Lote">
                <i class="fa fa-download"></i></a>';

            $acciontext = "'deleteBancos'";
            $btndelete = '<button type="button" id="delete' . $lots->id . '" data-href="' . route('lotebanco.destroy', $lots->id) . '" onclick="AccionesAudit(' . $acciontext . ',' . $lots->id . ');" 
                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Eliminar Consolidado">
                        <i class="fa fa-times"></i>
                    </button>';

            $btngroup = '<div class="btn-group">' . $btnview . $btndelete . '</div>';
            $row["acciones"] = $btngroup;
            $data[] = $row;
        }

        return json_encode([$display, $data]);
    }
    public function bankExport($id)
    {

        //SACAMOS EL NOMBRE DEL LOTE PARA IDENTIFICAR EL ARCHIVO
        $lote = LoteBancoModel::find($id);
        $nombre_lote = $lote->lote_banco;
        $bancos = BancoModel::where('lotebanco_id', $lote->id)->get();
        return Excel::download(new BancoExport($bancos), $nombre_lote . '' . '.xlsx');
    }
    public function lotebancoDestroy($id)
    {

        BancoModel::where('lotebanco_id', $id)->delete();
        LoteModel::where('lotebanco_id', $id)->update(['estatus' => 'AUDITADO', 'lotebanco_id' => null]);

        LoteBancoModel::where('id', $id)->delete();
        return json_encode("El lote banco fue eliminado exitosamente.");
    }
    public function changingConsolidadoPay(Request $request)
    {

        try {
            //RECORREMOS TODOS LOS LOTES EN PROCESO DE PAGO SELECCIONADOS POR ID
            foreach ($request->consolidado as $id) {
                //ACTUALIZAMOS EL ESTATUS DE LOS LOTE BANCO A PAGADO
                $estatus_lote_banco = LoteBancoModel::where('id', $id)->first();
                $estatus_lote_banco->estatus    = "PAGADO";
                $estatus_lote_banco->banco      = json_encode($request->banco);
                $estatus_lote_banco->fecha_pago = carbon::createFromFormat('d/m/Y', $request->fechapago)->format('d/m/Y');
                $estatus_lote_banco->save();

                LoteModel::where('lotebanco_id', $id)->update([
                    'estatus' => 'PAGADO',
                ]);

                //ACTUALIZAMOS EL ESTATUS DE LOS BANCOS A PAGADO IGUALMENTE
                BancoModel::where('lotebanco_id', $id)->update([
                    'estatus' => 'PAGADO',
                ]);

                //SACAMOS LOS ID LOTES DE LA TABLA COM_LOTES DEPENDE DEL COM_LOTE_BANCO SELECCIONADO
                $id_lotes = LoteModel::select('id')->where('lotebanco_id', $id)->get()->toArray();
                //YA QUE TENEMOS LOS IDS RESPECTIVOS DE LOS LOTES ACTUALIZAMOS EL ESTATUS INCIDENCIA A 0 QUE SIGNIFICA PAGADO
                OperacionesComisiones::whereIn('lote_id', $id_lotes)->update(['incidencia' => 0]);
            }
            \Session::flash('success', "Lotes de comisiones pagados exitosamente");
            return back();
        } catch (\Exception $e) {
            \Session::flash('error', "Error al procesar, " . $e->getMessage());
            return back();
        }
    }

    ////pestaña incidencias
    public function bankConsultIncidencias(Request $request)
    {

        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->fechacarga)->startOfMonth();
        $mes    = $fechaCarbon->copy()->format('F');
        $anio   = $fechaCarbon->copy()->format('Y');

        //MANDAMOS LOS LOTE BANCO PARA CARGAR LAS OBSERVACIONES
        $lotes       = LoteBancoModel::where('mes', $mes)->where('anio', $anio)->get();
        $count_lots = $lotes->count();

        $row = array();
        $data = array();
        $display = empty($lotes) ? true : false;

        foreach ($lotes as $lots) {

            $row["id"] = $lots->id;
            $row["check"] = '<div class="custom-control custom-checkbox">
            <input class="custom-control-input check" name="comisiones[]" type="checkbox" id="comisiones' . $lots->id . '" value="' . $lots->id . '">
            <label for="comisiones' . $lots->id . '" class="custom-control-label"></label></div>';
            $row["nombre"] = $lots->lote_banco;
            $row["banco"] = $lots->banco != null ? json_decode($lots->banco) : "-";
            $row["estatus"] = $lots->estatus;
            $row["fecha"] = trans('meses.' . $lots->mes) . " " . $lots->anio;
            $row["fechapago"] = $lots->fecha_pago != null ? $lots->fecha_pago : "-";

            $btnview = '<a type="button" id="view' . $lots->id . '" href="' . route('comisiones.bank.observaciones', $lots->id) . '"                      
                class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Descargar Lote">
                <i class="fa fa-download"></i></a>';


            $acciontext = "'discardbank'";
            $btnrestore = '<button type="button" id="restore' . $lots->id . '" data-href="' . route('lote.banco.discard', $lots->id) . '" onclick="AccionesAudit(' . $acciontext . ',' . $lots->id . ');" 
                        class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Revertir estatus del Lote">
                       <i class="fa fa-undo"></i>
                    </button>';

            $btnList = '<a class="btn btn-primary btn-sm" href="' . route('personal.incidencia', $lots->id) . '" data-toggle="tooltip" '
                . 'data-placement="top" target="_blank" title="Ver Incidencias del Lote"><i class="fa fa-list"></i></a>';

            if ($display) {
                /// true para ocultar todos los botones cuando esta pagado
                $btngroup = '<div class="btn-group">' . $btnview . '</div>';
            } else {
                $btngroup = '<div class="btn-group">' . $btnview . $btnrestore . $btnList . '</div>';
            }


            $row["acciones"] = $btngroup;

            $data[] = $row;
        }
        return json_encode([$display, $data]);
    }
    ///ver Archivo de incidencias
    public function observacionesExport($id)
    {

        //SACAMOS EL NOMBRE DEL LOTE PARA IDENTIFICAR EL ARCHIVO
        $lote = LoteBancoModel::select('lote_banco')->where('id', $id)->first();
        $nombre_lote = $lote->lote_banco;
        $bancos = BancoModel::where('lotebanco_id', $id)->get();

        return Excel::download(new ObservacionesExport($bancos), 'Incidencias Bancarias' . ' ' . $nombre_lote . '.xlsx');
    }
    ///revertir el consolidado pagado a en proceso.
    public function loteBankIncidenciasDiscard($id)
    {

        //REALIZAMOS PRIMERO EL PROCESO INVERSO DE EN PROCESO A PAGADO, ES DECIR DE PAGADO A EN PROCESO
        //actualizamos el estatus del lote banco seleccionado a EN PROCESO

        $estatus_lote_banco = LoteBancoModel::where('id', $id)->first();
        $estatus_lote_banco->estatus    = "EN PROCESO";
        $estatus_lote_banco->banco      = null;
        $estatus_lote_banco->fecha_pago = null;
        $estatus_lote_banco->save();

        //ACTUALIZAMOS EL ESTATUS DE LOS LOTES A EN PROCESO

        LoteModel::where('lotebanco_id', $id)->update([
            'estatus' => 'EN PROCESO',
        ]);
        //ACTUALIZAMOS EL ESTATUS DE LOS BANCOS A EN PROCESO IGUALMENTE

        BancoModel::where('lotebanco_id', $id)->update([
            'estatus' => 'EN PROCESO',
        ]);
        //SACAMOS LOS ID LOTES DE LA TABLA COM_LOTES DEPENDE DEL COM_LOTE_BANCO SELECCIONADO
        $id_lotes = LoteModel::select('id')->where('lotebanco_id', $id)->get()->toArray();

        //YA QUE TENEMOS LOS IDS RESPECTIVOS DE LOS LOTES ACTUALIZAMOS EL ESTATUS INCIDENCIA A 3 QUE SIGNIFICA AUDITADO
        OperacionesComisiones::whereIn('lote_id', $id_lotes)->update(['incidencia' => 3]);

        /* FIN DEL PRIMER PASO */
        // EL SIGUIENTE Y ULTIMO PASO ES PASAR DE EN PROCESO A AUDITADO

        BancoModel::where('lotebanco_id', $id)->delete();
        LoteModel::where('lotebanco_id', $id)->update(['estatus' => 'AUDITADO']);
        LoteModel::where('lotebanco_id', $id)->update(['lotebanco_id' => null]);
        LoteBancoModel::where('id', $id)->delete();

        return json_encode("Estatus del lote banco actualizado exitosamente");
    }
    ////tabla que despliega las incidencias por lote  consolidado de pagos -> cargar incidencias
    public function personalIncidencia($id)
    {
        $lote_name = LoteBancoModel::select('lote_banco')->where('id', $id)->first();
        $nombre_lote = $lote_name->lote_banco;
        //BUSCAMOS EL PERSONAL QUE TENGA ESTATUS DE INCIDENCIA EN LA TABLA COM_BANCO
        $incidencias = BancoModel::where('lotebanco_id', $id)->whereIn('estatus', ['INCIDENCIA', 'PAGADO'])->where('observaciones', '!=', null)->get();
        return view('comisiones.bancos.incidenciaslist', compact('incidencias', 'nombre_lote', 'id'));
    }
    public function changeIncidencia(Request $request)
    {

        //REALIZAMOS EL PROCESO INVERSO DE PAGADO A INCIDENCIA, SE PASARAN LOS LOTES DE INCIDENCIA A PAGADO
        if (isset($request->incidencias)) {

            //CAMBIAMOS EL ESTATOS DEL ID BANCO A PAGADO
            BancoModel::where('lotebanco_id', $request->lotebanco)->update(['estatus' => 'PAGADO']);

            $id_lotes = LoteModel::select('id')->where('lotebanco_id', $request->lotebanco)->get()->pluck('id');

            foreach ($request->incidencias as $id) {

                $cedulapersonal = BancoModel::find($id)->cedula;

                OperacionesComisiones::whereIn('lote_id', $id_lotes)
                    ->where('cedula', $cedulapersonal)->update(['incidencia' => 0]);
            }

            \Session::flash('success', 'Cambio de Estadus realizado exitosamente');
            return back();
        } else {
            \Session::flash('info', 'No fue seleccionado ningun dato.');
            return back();
        }
    }
    public function bankProcessIncidencias(Request $request)
    {

        try {
            Excel::import(new ObservacionesImport($request->all()), request()->file('incidencias'));
            return back();
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) { //retorno de las reglas de validacion
            return back()->withErrors($e->failures());
        } catch (\App\Exceptions\ValidateAditionals $e) { //catch de validaciones adicionales del archivo
            // Errores de tus validaciones personalizadas
            return redirect()->back()->withErrors($e->getErrors());
        } catch (\Exception $t) { //errornes comunes         
            $mensaje = str_replace("\n", 'and', $t->getMessage());
            \Session::flash('error', "Error: " . $mensaje !== null ? $mensaje : $t->getMessage());
            return back();
        }
    }
    ///pestaña reporte de bancos
    public function bankReport(Request $request)
    {
        try {
            $fechaCarbon = Carbon::createFromFormat('m/Y', $request->fechadescarga)->startOfMonth();
            $mes    = $fechaCarbon->copy()->format('F');
            $anio   = $fechaCarbon->copy()->format('Y');
            $estattus = null;
            switch ($request->estatus) {
                case "todos":
                    $bancos = LoteBancoModel::where('mes', $mes)
                        ->where('anio', $anio)->get();
                    $estattus = '';
                    break;
                default:
                    $bancos = LoteBancoModel::where('mes', $fechaCarbon->copy()->format('F'))
                        ->where('anio', $fechaCarbon->copy()->format('Y'))
                        ->where('estatus', $request->estatus)->get();
                    $estattus = $request->estatus;
                    break;
            }

            return Excel::download(new LoteBancoExport($bancos), 'Lotes Bancarios ' . trans('meses.' . $mes) . ' ' . $anio . ' ' . $estattus . '.xlsx');
        } catch (\Exception $e) {
            \Session::flash('error', 'Ocurrio un problema la Descargar el archivo ' . $e->getMessage());
            return Back();
        }
    }

    ///reportes de comisiones
    public function ReportesExport()
    {

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);

        if ($nivel) {
            $areas = PoliceEstructure::VerifiedGerenciasPermissions($nivel, $user);
        } else {
            return abort(403, "No cuenta con los Nivel necesario para acceder a estos recursos.");
        }

        /// le envio 4 meses de descarga para que el selector al cambiar el skill no se actualice.
        $mes        =  $this->Numero_meses_anteriores_permitido;
        $permiso    = "true";

        return view('comisiones.reportes.index')->with(["areas" => $areas, "esquema" => $this->esquema, "timeMes" => $mes, "permiso" => $permiso]);
    }

    public function GetSemanas(Request $request)
    {

        $fecharecibida = Carbon::createfromFormat('m/Y', $request->mes);

        $tipo_periodo = isset($request->tipo_periodo) ? $request->tipo_periodo : SkillModels::find($request->skill)->tipo_periodo;

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        //validacion de permisos para cargas demoradas se mostrara 1 si no tiene permisos, y 6 si los tiene
        $intMeses = $user->hasPermission('comisiones.cargar.demoradas') == true ?  $this->Numero_meses_anteriores_permitido : $this->Numero_meses_anteriores_no_permitido;

        switch ($tipo_periodo) {
            case "Semanal":
                //DETERMINAMOS EN QUE SEMANA ESTAMOS DEL MES EN CURSO
                $semanaencurso = $fecharecibida->copy()->weekOfMonth;
                switch ($semanaencurso) {
                    case 1:
                        $minDate = $fecharecibida->copy()->subMonth($intMeses)->format('m/Y');
                        $maxDate = $fecharecibida->copy()->format('m/Y');
                        $type_semana = 1;
                        break;
                    default:
                        $minDate = $fecharecibida->copy()->subMonth($intMeses)->format('m/Y');
                        $maxDate = $fecharecibida->copy()->format('m/Y');
                        $type_semana = 2;
                        break;
                }
                $year   = $fecharecibida->copy()->format('Y');
                $month  = $fecharecibida->copy()->format('m');

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

                break;
            case "Quincenal": //SI EL TIPO DE PERIODO QUE VIENE ES QUINCENAL
                $quicena_en_curso = 3;
                ///validamos si el mes recibido es igual al a fecha actual, en caso de ser diferentes se mostraran ambas quincenas
                $opcion_corte_show = ["1" => "1ra. Quincena", "2" => "2da. Quincena"];
                break;
            default:
                return json_encode(abort(500, "El Skill seleccionado No cuenta con el esquema de pago."));
                break;
        }

        return json_encode([
            'tipo_periodo'      => $tipo_periodo,
            'quincena_en_curso' => $quicena_en_curso ?? null,
            'minDate'           => $minDate  ?? null,
            'maxDate'           => $maxDate  ?? null,
            'opcion_corte_show' => $opcion_corte_show ?? null,
            'arreglo_semanal'   => $semanasArray ?? null,
            'semana_en_curso'   => null,
        ]);
    }
    ////funcion que cambia elcorte si input mes_report cambia
    public function selectorPeriodos(Request $request)
    {

        //FUNCION QUE CALCULA LA CANTIDAD DE SEMANAS EN UN MES

        $mes = $request->mes;

        $particion_fecha = explode("/", $mes);

        $year = $particion_fecha[1]; // Año seleccionado
        $month = $particion_fecha[0]; // Mes seleccionado

        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year); // Obtener el número de días en el mes seleccionado
        $weeks_in_month = ceil($days_in_month / 7); // Calcular el número de semanas redondeando hacia arriba

        return json_encode($weeks_in_month);
    }

    public function exportProcess(Request $request)
    {

        $user = ENV('DEV_USER_CHANGE', false) ?
            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
            \Auth::user();

        $nivel  = PoliceEstructure::VerifiedNivelPermissions($user);

        $fecha  = carbon::createfromformat('m/Y', $request->mes_report)->startofmonth();
        $anio   = $fecha->copy()->format('Y');
        $mes    = $fecha->copy()->format('F');

        $fechareporte   = $fecha->copy()->format('m-Y');
        $nombre_archivo = "";

        $data   = null;
        $sql = OperacionesComisiones::where('tipo_periodo', $request->tipo_periodo_report)
            ->where('mes', $mes)
            ->where('anio', $anio);

        switch ($nivel) {
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                if ($request->area_trabajo_report == "0" && $request->carteras_report == "0") {
                    $nombre_archivo = "Comisiones Generales " . $request->tipo_periodo_report . " " . $fechareporte;

                    $sql = $sql->whereIn('skill_id', $skillPermisado)->wherein('personal_id', $idPersonals);
                    ///SI VIENE EL TIPO DE CORTE CON DATOS
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                if ($request->area_trabajo_report != "0" && $request->carteras_report == "0") {

                    $cartera = AreatrabajoModel::find($request->area_trabajo_report);
                    $gerencia = $cartera->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)
                        ->whereIn('skill_id', $skillPermisado)
                        ->wherein('personal_id', $idPersonals);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }
                ///SI GERENCIA Y CARTERA TIENEN DATOS
                if ($request->area_trabajo_report != "0" && $request->carteras_report != "0") {

                    $cartera = CarteraModel::find($request->carteras_report);
                    $nombre_cartera = $cartera->nombre_cartera;
                    $gerencia = $cartera->RelationGerencias->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $nombre_cartera . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)
                        ->where('campana_id', $request->carteras_report)
                        ->whereIn('skill_id', $skillPermisado)
                        ->wherein('personal_id', $idPersonals);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                $data = $sql->get();

                break; //por skill asignado y personal asignado
            case 3:
                $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                ///SELECTORES EN TODOS
                if ($request->area_trabajo_report == "0" && $request->carteras_report == "0") {
                    $nombre_archivo = "Comisiones Generales " . $request->tipo_periodo_report . " " . $fechareporte;

                    $sql = $sql->whereIn('skill_id', $skillsautorize);
                    ///SI VIENE EL TIPO DE CORTE CON DATOS
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                if ($request->area_trabajo_report != "0" && $request->carteras_report == "0") {

                    $cartera = AreatrabajoModel::find($request->area_trabajo_report);
                    $gerencia = $cartera->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)->whereIn('skill_id', $skillsautorize);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }
                ///SI GERENCIA Y CARTERA TIENEN DATOS
                if ($request->area_trabajo_report != "0" && $request->carteras_report != "0") {

                    $cartera = CarteraModel::find($request->carteras_report);
                    $nombre_cartera = $cartera->nombre_cartera;
                    $gerencia = $cartera->RelationGerencias->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $nombre_cartera . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)
                        ->where('campana_id', $request->carteras_report)
                        ->whereIn('skill_id', $skillsautorize);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                $data = $sql->get();
                break; //por skill asignado
            case 2: /// personal asignado

                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills = $carterasPersonal[2];
                $personal = $carterasPersonal[3];

                if ($request->area_trabajo_report == "0" && $request->carteras_report == "0") {
                    $nombre_archivo = "Comisiones Generales " . $request->tipo_periodo_report . " " . $fechareporte;
                    ///SI VIENE EL TIPO DE CORTE CON DATOS

                    $sql = $sql->whereIn('personal_id', $personal);

                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                if ($request->area_trabajo_report != "0" && $request->carteras_report == "0") {

                    $cartera = AreatrabajoModel::find($request->area_trabajo_report);
                    $gerencia = $cartera->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)->whereIn('personal_id', $personal);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }
                ///SI GERENCIA Y CARTERA TIENEN DATOS
                if ($request->area_trabajo_report != "0" && $request->carteras_report != "0") {

                    $cartera = CarteraModel::find($request->carteras_report);
                    $nombre_cartera = $cartera->nombre_cartera;
                    $gerencia = $cartera->RelationGerencias->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $nombre_cartera . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)
                        ->where('campana_id', $request->carteras_report)
                        ->whereIn('personal_id', $personal);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                $data = $sql->get();

                break;
            case 1:

                ////4 SELECTORES EN TODOS | PERIODO_REPORT = SEMENAL O QUINCENAL | CORTE_REPORT = 1 AL 5 SEMANAL, 1 AL 2 QUINCENAL, 0 TODO
                if ($request->area_trabajo_report == "0" && $request->carteras_report == "0") {

                    $nombre_archivo = "Comisiones Generales " . $request->tipo_periodo_report . " " . $fechareporte;
                    ///SI VIENE EL TIPO DE CORTE CON DATOS
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }
                ///SI GERENCIA Y CARTERA TIENEN DATOS
                if ($request->area_trabajo_report != "0" && $request->carteras_report == "0") {

                    $cartera = AreatrabajoModel::find($request->area_trabajo_report);
                    $gerencia = $cartera->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $request->tipo_periodo_report . " del " . $fechareporte;
                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                if ($request->area_trabajo_report != "0" && $request->carteras_report != "0") {
                    $cartera =  CarteraModel::find($request->carteras_report);
                    $nombre_cartera = $cartera->nombre_cartera;
                    $gerencia = $cartera->RelationGerencias->nombre_campana;

                    $nombre_archivo = "Comisiones Generales de" . $gerencia . " " . $nombre_cartera . " " . $request->tipo_periodo_report . " del " . $fechareporte;

                    $sql = $sql->where('gerencia_id', $request->area_trabajo_report)
                        ->where('campana_id', $request->carteras_report);

                    ///SI VIENE EL TIPO DE CORTE CON DATOS  
                    if ($request->corte_report !== "0") {
                        $sql = $sql->where('periodo', $request->corte_report);
                    }
                }

                $data = $sql->get();

                break;
            default:
                ///retorna el array vacio, no posee permisos.
                \Session::flash('error', abort(500, "No posee permisos para acceder a esta información"));
                return back();
                break;
        }

        return Excel::download(new ComisionesGeneralExport($data), $nombre_archivo . '.xlsx');
    }

    ///REPORTE DE INCIDENCIAS BANCARIAS
    public function Incidencias()
    {

        return view('comisiones.reportes.incidencias');
    }
    public function IncidenciasShow(Request $request)
    {


        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->periodo)->startOfMonth();

        $mes    = $fechaCarbon->copy()->format('F');
        $anio   = $fechaCarbon->copy()->format('Y');

        $lots = LoteBancoModel::where('mes', $mes)->where('anio', $anio)
            ->where('estatus', 'PAGADO CON INCIDENCIAS')->get();

        $row = array();
        $data = array();
        $lista_bancos = array();

        foreach ($lots as $incidencias) {
            $row["id"] = $incidencias->id;
            $row["nombrelote"] = $incidencias->lote_banco;
            $row["periodo"] = $request->periodo;

            $lista_bancos = json_decode($incidencias->banco);
            $row["banco"] =  !empty($lista_bancos) ? implode('|', $lista_bancos) : "-";

            $row["fechapago"]   = $incidencias->fecha_pago != null ? carbon::createfromFormat('d/m/Y', $incidencias->fecha_pago)->format('d/m/Y') : "-";
            $row["estatus"]     = $incidencias->estatus;

            $btnview = '<a type="button" id="view' . $incidencias->id . '" href="' . route('comisiones.bank.observaciones', $incidencias->id) . '"                      
                class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Lote">
                <i class="fa fa-download"></i></a>';

            $btngroup = '<div class="btn-group">' . $btnview . '</div>';
            $row["acciones"]    = $btngroup;

            $data[] = $row;
        }

        return json_encode($data);
    }

    public function EstimatePayments(Request $request)
    {

        $gerencias  = AreaTrabajoModel::pluck('nombre_campana', 'id')->toArray();
        $skills     = SkillModels::pluck('nombre_skill', 'id')->toArray();

        $fechaCarbon = Carbon::createFromFormat('m/Y', $request->mes)->startOfMonth();
        $anio = $fechaCarbon->copy()->format('Y'); // Año seleccionado
        $mes = $fechaCarbon->copy()->format('F');

        //MANDAMOS LOS LOTES CON EL ESTATUS SELECCIONADO A LA VISTA
        $sql = LoteModel::select('id', 'skill_id', 'tipo_periodo', 'periodo')
            ->where('anio', $anio)
            ->where('mes', $mes);

        $lotesQuincenales = (clone $sql)->where('tipo_periodo', 'Quincenal')->get();

        $lotesSemanales = (clone $sql)->where('tipo_periodo', 'Semanal')->get();

        $TableQuincenal = array();
        $TableSemanal = array();

        #lectura quincenal
        $totales = OperacionesComisiones::selectraw('sum(total_usd) as totalpago, skill_id, gerencia_id, tipo_periodo,periodo')
            ->wherein('lote_id', $lotesQuincenales->pluck('id'))->groupby('skill_id', 'periodo')->get();


        foreach ($totales as $key => $quincenal) {
            $montoAnterior = 0;
            $TableQuincenal[$quincenal->gerencia_id]["gerencia"] = $gerencias[$quincenal->gerencia_id];
            $TableQuincenal[$quincenal->gerencia_id][$quincenal->skill_id]["total"] = 0;
            $TableQuincenal[$quincenal->gerencia_id][$quincenal->skill_id]["skill"] = $skills[$quincenal->skill_id];
            $TableQuincenal[$quincenal->gerencia_id][$quincenal->skill_id][$quincenal->tipo_periodo][$quincenal->periodo] = $quincenal->totalpago;
            $TableQuincenal[$quincenal->gerencia_id][$quincenal->skill_id]["total"] = $totales->where('skill_id', $quincenal->skill_id)->sum('totalpago');
        }

        ##lectura tabla Semanal
        $totalessem = OperacionesComisiones::selectraw('sum(total_usd) as totalpago, skill_id, gerencia_id, tipo_periodo,periodo')
            ->wherein('lote_id', $lotesSemanales->pluck('id'))->groupby('skill_id', 'periodo')->get();

        foreach ($totalessem as $key => $semanal) {
            $montoAnterior = 0;
            $TableSemanal[$semanal->gerencia_id]["gerencia"] = $gerencias[$semanal->gerencia_id];
            $TableSemanal[$semanal->gerencia_id][$semanal->skill_id]["total"] = 0;
            $TableSemanal[$semanal->gerencia_id][$semanal->skill_id]["skill"] = $skills[$semanal->skill_id];
            $TableSemanal[$semanal->gerencia_id][$semanal->skill_id][$semanal->tipo_periodo][$semanal->periodo] = $semanal->totalpago;
            $TableSemanal[$semanal->gerencia_id][$semanal->skill_id]["total"] = $totalessem->where('skill_id', $semanal->skill_id)->sum('totalpago');
        }

        return json_encode([$TableQuincenal, $TableSemanal]);
    }
}
