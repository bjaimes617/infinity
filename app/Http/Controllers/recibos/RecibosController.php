<?php

namespace App\Http\Controllers\recibos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RecibosImport;
use App\Models\personal\NominaModel;
use App\Models\personal\PersonalModel;
use App\Models\recibos\PeriodosModels;
use App\Models\recibos\AsigDeducModel;
use App\Models\recibos\RecibosNominaModel;
use App\Models\recibos\ContNominaModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExportNominas;
use Carbon\Carbon;

class RecibosController extends Controller {

    private $mes, $anio;
    ///carga masiva manual de los recibos
    public function index() {
        $nomina = NominaModel::all();
        $tiponomina = ContNominaModels::where('active', 1)->get();
        return view('recibos.index')->with(["nomina" => $nomina, "tiponomina" => $tiponomina]);
    }

    public function create(Request $request) {
        Excel::import(new RecibosImport($request), request()->file('archivo'));
        return redirect()->route('recibos.index');
    }

    ///activar periodos de recibos
    public function ActiveIndex() {

        $cortes = config('app.cortes');
        $nomina = NominaModel::all();
        $tiponomina = ContNominaModels::where('active', 1)->get();

        return view('recibos.administracion.active')->with(["cortes" => $cortes, "nomina" => $nomina,
            "tiponomina" => $tiponomina])->render();
    }

    public function SearchPeriodos(Request $request) {

        try {
            $periodo = array();
            if (in_array($request->tprecibo, [90, "90A"])) {

                $arrayBegin = carbon::createFromFormat('Y', $request->mes, 'UTC');
                $this->anio = $arrayBegin->copy()->format('Y');
                $this->mes = $arrayBegin->copy()->format('F');

                $periodos = PeriodosModels::where('rp_cont_nomina', $request->tprecibo)
                                ->where('anio', $this->anio)
                                ->where('tiponomina', $request->nomina)->get();
            } else {
                $arrayBegin = carbon::createFromFormat('m/Y', $request->mes, 'UTC');
                $this->mes = $arrayBegin->copy()->format('F');
                $this->anio = $arrayBegin->copy()->format('Y');

                $periodos = PeriodosModels::where('rp_cont_nomina', $request->tprecibo)
                                ->where('mes', $this->mes)
                                ->where('anio', $this->anio)
                                ->where('tiponomina', $request->nomina)->get();
            }

            $row = array();
            $data = array();

            foreach ($periodos as $periodo) {
                $row["id"] = $periodo->id;
                switch ($periodo->corte) {
                    case 1:
                        $row["corte"] = "1ra. Quincena | " . trans('meses.' . $this->mes) . " |" . $this->anio;
                        break;
                    case 2:
                        $row["corte"] = "2da. Quincena | " . trans('meses.' . $this->mes) . " |" . $this->anio;
                        break;
                    case 4:
                        // dd($request->tprecibo == "90A" ? $periodo->mes : $this->mes);
                        //solo aplicara el mes del periodo buscado si es anticipo de utilidades, par utilidades normales aplica enero
                        $this->mes = $request->tprecibo == "90A" ? $periodo->mes : $this->mes;
                        $row["corte"] = "Pago Unico | " . trans('meses.' . $this->mes) . " |" . $this->anio;
                        break;
                }

                $row["tprecibo"] = $periodo->RelationTipoRecibos()->where('nomina_id', $request->nomina)->first()->descripcion;
                $row["nomina"] = $periodo->RelationDirNomina->nombre_nomina;
                $row["descripcion"] = $periodo->descripcion;
                $active = $periodo->active == 1 ? "checked" : "";
                $row["estatus"] = '<input type="checkbox" class="periodoactive" ' . $active . ' onChange="ChangePeriodoActive(' . $periodo->id . ')" id="periodoactive' . $periodo->id . '"  name="periodoactive' . $periodo->id . '" data-href="' . route('recibos.activacion.update', $periodo->id) . '" data-value="' . $periodo->id . '" data-bootstrap-switch  data-off-color="danger" data-on-color="success">';
                $row["accion"] = '<form action="' . route('recibos.activacion.delete', $periodo->id) . '" method="POST" id="deleted' . $periodo->id . '">'
                        . '<input type="hidden" name="_method" value="delete">'
                        . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
                        . '<button type="button" class="btn btn-md btn-danger" onclick="DeletePeriodo(' . $periodo->id . ');" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="far fa-times-circle"></i></button>'
                        . '</form>';
                $data[] = $row;
            }
            return json_encode($data);
        } catch (\Exception $e) {
            return json_encode(abort(500, "Ocurrio Un error al realizar la consulta. " . $e->getMessage()));
        }
    }

    public function ActiveUpdate($id, $type) {

        $periodo = PeriodosModels::find($id);
        $periodo->active = $type;
        $periodo->save();
        if ($type == 1) {
            return json_encode(["type" => "success", "menssage" => "El Periodo ya ha sido activado. Se podra Acceder a los recibos de pago."]);
        } else {
            return json_encode(["type" => "info", "menssage" => "El Periodo ha sido Desactivado. No se podra Acceder a los recibos de pago."]);
        }
    }

    ///Generar recibo de pagos como Gerente
    public function GenerarIndex() {

        $cortes = config('app.cortes');
        $nomina = NominaModel::all();
        $tiponomina = ContNominaModels::where('active', 1)->get();

        $initPersonal = PersonalModel::where('cedula_empleado', \Auth::user()->cedula)->first();

        $personal = $initPersonal->RelationPersonalacargoGerente()->wherenotin('situacion_contractual', [3])->where('estatus_id', 1)->orderby('primer_nombre', 'ASC')->get();

        return view('recibos.generate')->with(["personal" => $personal, "nomina" => $nomina,
                    "tiponomina" => $tiponomina, "cortes" => $cortes]);
    }

    public function GenerarCreate(Request $request) {

        try {
            $this->ValuesProtocols($request);

            $cedula = PersonalModel::find($request->persona);
            $nombreConcat = $cedula->primer_nombre . ' ' . $cedula->primer_apellido;
            switch ($request->corte) {
                case 1:
                case 2:
                    return $this->ConsultsPeriod($request, $cedula, $nombreConcat);
                    break;
                case 3:
                case 4:
                    return $this->ConsultAllPeriod($request, $cedula, $nombreConcat);
                    break;
            }
        } catch (\Exception $e) {

            \Session::flash('error', 'Ocurrio un problema al generar el Recibo ' . $e->getMessage());
            return back();
        }
    }

    ///Generar recibo de pago por Cedula
    public function IndexAdministracion() {

        $cortes = config('app.cortes');
        $nomina = NominaModel::all();
        $tiponomina = ContNominaModels::where('active', 1)->get();

        return view('recibos.administracion.index')->with(["nomina" => $nomina,
                    "tiponomina" => $tiponomina, "cortes" => $cortes]);
    }

    public function CreateAdministracion(Request $request) {

        try {
            $this->ValuesProtocols($request);

            $cedula = PersonalModel::where('cedula_empleado', $request->cedula)->first();
            $nombreConcat = $cedula->primer_nombre . ' ' . $cedula->primer_apellido;
            switch ($request->corte) {
                case 1:
                case 2:
                    return $this->ConsultsPeriod($request, $cedula, $nombreConcat);
                    break;
                case 3:
                case 4:
                    return $this->ConsultAllPeriod($request, $cedula, $nombreConcat);
                    break;
            }
        } catch (\Exception $e) {

            \Session::flash('error', 'Ocurrio un problema al generar el Recibo ' . $e->getMessage());
            return back();
        }
    }

    ///protocolo de fechas y cortes
    private function ValuesProtocols($request) {

        $tipoPago = ContNominaModels::where('codigo', $request->tprecibo)->where('nomina_id', $request->nomina)->first();
        switch ($tipoPago->type) {
            case "Q":
                $arrayBegin = carbon::createFromFormat('m/Y', $request->mes);
                $this->mes = $arrayBegin->copy()->format('F');
                $this->anio = $arrayBegin->copy()->format('Y');
                switch ($request->corte) {
                    case 1;
                        $this->fechainicio = $arrayBegin->copy()->StartOfMonth();
                        $this->fechacorte = $arrayBegin->copy()->StartOfMonth()->Add('14 Days');
                        break;
                    case 2:
                        $this->fechainicio = $arrayBegin->copy()->StartOfMonth()->Add('15 Days');
                        $this->fechacorte = $arrayBegin->copy()->EndOfMonth();
                        break;
                    case 3:
                        $this->fechainicio = $arrayBegin->copy()->StartOfMonth();
                        $this->fechacorte = $arrayBegin->copy()->EndOfMonth();
                        break;
                }
                break;
            case "M":
                $arrayBegin = carbon::createFromFormat('m/Y', $request->mes);
                $this->mes = $arrayBegin->copy()->format('F');
                $this->anio = $arrayBegin->copy()->format('Y');
                $this->fechainicio = $arrayBegin->copy()->StartOfMonth();
                $this->fechacorte = $arrayBegin->copy()->EndOfMonth();
                break;
            case "A":
                $arrayBegin = carbon::createFromFormat('Y', $request->mes);
                $this->anio = $arrayBegin->copy()->format('Y');
                $this->fechainicio = $arrayBegin->copy()->StartofYear();
                $this->fechacorte = $arrayBegin->copy()->EndofYear();
                break;
        }
    }

    ///consultas por cortes
    private function ConsultsPeriod($request, $cedula, $nombreConcat) {
        
        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   
        
        if (PeriodosModels::where('corte', $request["corte"])
                        ->where('rp_cont_nomina', $request["tprecibo"])
                        ->where('mes', $this->mes)
                        ->where('anio', $this->anio)
                        ->where('tiponomina', $request["nomina"])->exists()) {

            $sql = PeriodosModels::where('corte', $request["corte"])
                            ->where('rp_cont_nomina', $request["tprecibo"])
                            ->where('mes', $this->mes)
                            ->where('anio', $this->anio)
                            ->where('tiponomina', $request["nomina"])->first();

            $sql->load('RelationRecibosNominas');

            if ($sql->RelationRecibosNominas()->where('cedula', $cedula->cedula_empleado)->exists()) {

                $rn = $sql->RelationRecibosNominas()->where('cedula', $cedula->cedula_empleado)->first();
                $rn->load('RelationAsignaDeducs');

                $asig = $rn->RelationAsignaDeducs()->select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                ->groupby('rp_items_id')->orderBy('tipo', 'asc')->orderBy('coditem', 'ASC')->get();

                $fechacorte = $this->fechacorte;
                $fechainicio = $this->fechainicio;
                $activesummonto = false;
                $tipocorte = $sql->corte;
                $directivo = $cedula->RelationCargos->directivo;
                ////si tengo permisos para emitir recibo sin activar periodo o esta activado 
                if ($user->hasPermission(['recibos.verificar.cargas']) || $sql->active === 1) {
                    return \PDF::loadView('recibos.pdf.index', compact('sql', 'rn', 'cedula', 'asig', 'directivo', 'fechainicio', 'tipocorte', 'fechacorte', 'activesummonto'))
                                    ->setPaper('letter')->stream($nombreConcat . '.pdf', array('Attachment' => 0));
                } else {
                    \Session::flash('info', 'No se encuentra Disponible el periodo Seleccionado, Intente más tarde.');
                    return back();
                }
            } else {
                \Session::flash('error', 'No se puede ubicar el recibo de pago de este personal');
                return back();
            }
        } else {
            \Session::flash('info', 'No Se puede ubicar la información sobre el recibo Consultado');
            return back();
        }
    }

    private function ConsultAllPeriod($request, $cedula, $nombreConcat) {
        ///validamos si el recibo es de tipo utilidades
        
        $user = ENV('DEV_USER_CHANGE',false) ? \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) : \Auth::user();   
        
        switch ($request["tprecibo"]) {
            case "90":                
                if (PeriodosModels::whereIn('corte', [1, 2, 4])
                    ->where('rp_cont_nomina', $request["tprecibo"])
                    ->where('anio', $this->anio)
                    ->where('tiponomina', $request["nomina"])->exists()) {

                    $sqls = PeriodosModels::wherein('corte', [1, 2, 4])
                                    ->where('rp_cont_nomina', $request["tprecibo"])
                                    ->where('anio', $this->anio)
                                    ->where('tiponomina', $request["nomina"])->get();
                    $idperiodos = $sqls->pluck('id');
                    ///nos traemos el ultimo recibo de pago para calcula
                    $sql = $sqls->last();

                    if (RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->exists()) {

                        $recibo = RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->get();
                        $rn = $recibo->last();

                        $idrecibo = $recibo->pluck('id');

                        $asig = AsigDeducModel::select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                        ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                        ->wherein('rp_nomina_id', $idrecibo)->groupby('rp_items_id')
                                        ->orderBy('tipo', 'asc')->orderBy('coditem', 'ASC')->get();

                        $fechacorte = $this->fechacorte;
                        $fechainicio = $this->fechainicio;

                        $activesummonto = true;
                        $tipocorte = $request->corte;
                        $directivo = $cedula->RelationCargos->directivo;
                        ////si tengo permisos para emitir recibo sin activar periodo o esta activado 
                        if ($user->hasPermission(['recibos.verificar.cargas']) || $sql->active === 1) {
                            return \PDF::loadView('recibos.pdf.index', compact('sql', 'rn', 'cedula', 'directivo', 'asig', 'tipocorte', 'fechainicio', 'tipocorte', 'fechacorte', 'activesummonto'))
                                            ->setPaper('letter')->stream($nombreConcat . '.pdf', array('Attachment' => 0));
                        } else {
                            \Session::flash('info', 'No se encuentra Disponible el periodo Seleccionado, Intente más tarde.');
                            return back();
                        }
                    } else {
                        \Session::flash('error', 'No se puede ubicar el recibo de pago de este personal');
                        return back();
                    }
                } else {
                    \Session::flash('info', 'No Se puede ubicar la información sobre el recibo Consultado');
                    return back();
                }
                break;
            case "90A":             
                  if (PeriodosModels::whereIn('corte', [1, 2, 4])
                                ->where('rp_cont_nomina', $request["tprecibo"])->where('anio', $this->anio)
                                ->where('tiponomina', $request["nomina"])->exists()) {

                    $sqls = PeriodosModels::wherein('corte', [1, 2, 4])
                                    ->where('rp_cont_nomina', $request["tprecibo"])
                                    ->where('anio', $this->anio)
                                    ->where('tiponomina', $request["nomina"])->get();
                    
                    $idperiodos = $sqls->pluck('id');
                    ///nos traemos el ultimo recibo de pago para calcula                     
                    if (RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->exists()) {

                        //$recibo = RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->get();                           
                        $datarecibos = array();
                        
                        foreach($sqls as $periodos){ 
                        
                            $rn = $periodos->RelationRecibosNominas()->where('cedula', $cedula->cedula_empleado)->first();
                            
                        $datarecibo[$periodos->id]["fechainicio"] = Carbon::create($rn->fec_ini)->format('d/m/Y');
                        $datarecibo[$periodos->id]["fechacorte"] = Carbon::create($rn->fec_fin)->format('d/m/Y');
                        $datarecibo[$periodos->id]["id"] = $rn->id;
                                                
                        $asig = AsigDeducModel::select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                ->where('rp_nomina_id', $rn->id)->groupby('rp_items_id')
                                ->orderBy('tipo', 'asc')->orderBy('coditem', 'ASC')->get();
                        
                            foreach($asig as $key => $asignaciones){
                                $datarecibo[$periodos->id]["asignaciones"][$key]["descripcion"] = $asignaciones->coditem ." - ". strtoupper($asignaciones->descripcion);
                                $datarecibo[$periodos->id]["asignaciones"][$key]["monto"] = $asignaciones->monto;
                                $datarecibo[$periodos->id]["asignaciones"][$key]["tipo"] = $asignaciones->tipo;
                            }
                        }                                                                 
                        $activesummonto = true;
                        $tipocorte = $request->corte;
                        $directivo = $cedula->RelationCargos->directivo;
                        ////si tengo permisos para emitir recibo sin activar periodo o esta activado 
                        if (\Auth::user()->hasPermission(['recibos.verificar.cargas']) || $sql->active === 1) {
                            return \PDF::loadView('recibos.pdf.anticipos', compact('sqls', 'cedula','datarecibo', 'directivo','tipocorte','activesummonto'))
                                            ->setPaper('letter')->stream($nombreConcat . '.pdf', array('Attachment' => 0));
                        } else {
                            \Session::flash('info', 'No se encuentra Disponible el periodo Seleccionado, Intente más tarde.');
                            return back();
                        }
                    } else {
                        \Session::flash('error', 'No se puede ubicar el recibo de pago de este personal');
                        return back();
                    }
                } else {
                    \Session::flash('info', 'No Se puede ubicar la información sobre el recibo Consultado');
                    return back();
                }
                break;
            default:              
                if (PeriodosModels::whereIn('corte', [1, 2, 4])
                                ->where('rp_cont_nomina', $request["tprecibo"])
                                ->where('mes', $this->mes)
                                ->where('anio', $this->anio)
                                ->where('tiponomina', $request["nomina"])->exists()) {

                    $sqls = PeriodosModels::wherein('corte', [1, 2, 4])
                                    ->where('rp_cont_nomina', $request["tprecibo"])
                                    ->where('mes', $this->mes)
                                    ->where('anio', $this->anio)
                                    ->where('tiponomina', $request["nomina"])->get();
                    $idperiodos = $sqls->pluck('id');
                    ///nos traemos el ultimo recibo de pago para calcula
                    $sql = $sqls->last();

                    if (RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->exists()) {

                        $recibo = RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->get();
                        $rn = $recibo->last();

                        $idrecibo = $recibo->pluck('id');

                        $asig = AsigDeducModel::select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                        ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                        ->wherein('rp_nomina_id', $idrecibo)->groupby('rp_items_id')
                                        ->orderBy('tipo', 'asc')->orderBy('coditem', 'ASC')->get();

                        if ($request["tprecibo"] == 91) {
                            $fechacorte = $rn->fec_ini != null ? carbon::create($rn->fec_ini) : null;
                            $fechainicio = $rn->fec_fin != null ? carbon::create($rn->fec_fin) : null;
                        } else {
                            $fechacorte = $this->fechacorte;
                            $fechainicio = $this->fechainicio;
                        }
                        $activesummonto = true;
                        $tipocorte = $request->corte;
                        $directivo = $cedula->RelationCargos->directivo;
                        ////si tengo permisos para emitir recibo sin activar periodo o esta activado 
                        if (\Auth::user()->hasPermission(['recibos.verificar.cargas']) || $sql->active === 1) {
                            return \PDF::loadView('recibos.pdf.index', compact('sql', 'rn', 'cedula', 'directivo', 'asig', 'tipocorte', 'fechainicio', 'tipocorte', 'fechacorte', 'activesummonto'))
                                            ->setPaper('letter')->stream($nombreConcat . '.pdf', array('Attachment' => 0));
                        } else {
                            \Session::flash('info', 'No se encuentra Disponible el periodo Seleccionado, Intente más tarde.');
                            return back();
                        }
                    } else {
                        \Session::flash('error', 'No se puede ubicar el recibo de pago de este personal');
                        return back();
                    }
                } else {
                    \Session::flash('info', 'No Se puede ubicar la información sobre el recibo Consultado');
                    return back();
                }
                break;
        }
    }

    ///elimina el periodo completo
    public function delete($id) {

        try {
            $periodo = PeriodosModels::find($id);
            $nominas = $periodo->RelationRecibosNominas();
            $nominasid = $nominas->pluck('id');
            $asignaciones = AsigDeducModel::whereIn('rp_nomina_id', $nominasid);
            $asignaciones->Delete();
            $nominas->Delete();
            $periodo->Delete();
            \Session::flash('success', 'Periodo de recibos eliminados exitosamente.');
            return back();
        } catch (\Exception $e) {
            \Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function IndexReporteAdministracion() {
        $cortes = config('app.cortes');
        $nomina = NominaModel::all();
        $tiponomina = ContNominaModels::where('active', 1)->get();        
       
        return view('recibos.reporte.index')->with(["nomina" => $nomina,
                "tiponomina" => $tiponomina,
                "cortes" => $cortes]);
    }

    public function CreateReporteAdministracion(Request $request) {
       
        try{


        $nomina = NominaModel::all()->pluck('nombre_nomina','id')->toArray();
        
        if (in_array($request->tprecibo, [90, "90A"])) {
            $arrayBegin = carbon::createFromFormat('Y', $request->mes, 'UTC');
            $this->anio = $arrayBegin->copy()->format('Y');
            $this->mes = $arrayBegin->copy()->format('F');

            $sql =  PeriodosModels::where('rp_cont_nomina', $request->tprecibo)
            ->where('anio', $this->anio);

                if($request->nomina !== "todos"){
                    $sql->where('tiponomina', $request->nomina);
                } 
                
                $idperiodos = $sql->pluck('id');
        } else {            
            $arrayBegin = carbon::createFromFormat('m/Y', $request->mes, 'UTC');
            $this->mes = $arrayBegin->copy()->format('F');
            $this->anio = $arrayBegin->copy()->format('Y');

            $sql = PeriodosModels::where('rp_cont_nomina', $request->tprecibo)
                ->where('mes', $this->mes)
                ->where('anio', $this->anio);
                if($request->nomina !== "todos"){                                    
                    $sql->where('tiponomina', $request->nomina);
                }

            $idperiodos = $sql->pluck('id');
            
        }     
     
        $totalItems=RecibosNominaModel::select('rp_items_id','descripcion')
        ->leftJoin('rp_asignaciones_deducciones','rp_asignaciones_deducciones.rp_nomina_id','rp_nomina.id')
        ->Join('rp_items','rp_asignaciones_deducciones.rp_items_id','rp_items.id')
        ->whereIn('rp_periodo_id', $idperiodos)->groupby('rp_items_id')->pluck('descripcion','rp_items_id')->unique();

        $Recibonomina = RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->get();

        $array = array();
        $structData = array();
        foreach ($Recibonomina as $DatosNomina) {
            $cedula = PersonalModel::where('cedula_empleado', $DatosNomina->cedula)->first();    
            $array["cedula"]    =  $cedula->cedula_empleado;
            $array["nombre"]    =  $cedula->primer_nombre . ' ' . $cedula->primer_apellido;
            $array["nomina"]    = $nomina[$cedula->nomina_id];
            $array["cargo"]     = $cedula->RelationCargos->nombre_cargo;
            $array["periodo"]   = carbon::createFromFormat('F', $DatosNomina->RelationNominatoPeriodo->mes)->format('m').'/'.$DatosNomina->RelationNominatoPeriodo->anio;
            
            switch ($DatosNomina->RelationNominatoPeriodo->corte) {
                case 1:
                    $array["corte"] = "1ra. Quincena ";
                    break;
                case 2:
                    $array["corte"] = "2da. Quincena ";
                    break;
                case 4:                      
                    $array["corte"] = "Pago Unico ";
                    break;
            }
            $array["gerencia"]  = $cedula->RelationAreas != null ? $cedula->RelationAreas->nombre_campana : "";
            $array["campana"]  = $cedula->RelationCartera != null ? $cedula->RelationCartera->nombre_cartera : "";
            if ($cedula->RelationCentroCosto == null) {
                $array["cecos"] = null;
            } else {
                $cod = array();          
                foreach ($cedula->RelationCentroCosto()->get() as $centroasignados) {                    
                    array_push($cod, $centroasignados->RelationCECO->cod_ceco);                 
                }
                $string = '|';
                $array["cecos"] = implode($string, $cod);
            }

            $array["deducciones"]   = (float)0.0;
            $array["asignaciones"]  = (float)0.0;
            $array["total"]         = (float)0.0;
            
            foreach($totalItems as $key => $value){
                $array[$key] =  (float)0.0;
            }         
            
           foreach($DatosNomina->RelationAsignaDeducs()->get() as $key => $value){
                $array[$value->rp_items_id] =  (float)$value->monto;
                if($value->RelationItems->tipo == "Asignación"){
                    $array["asignaciones"] += (float)$value->monto;
                }else{
                    $array["deducciones"] += (float)$value->monto;
                }
           }     
            $array["total"] = $array["asignaciones"] - $array["deducciones"];      
           array_push($structData,$array);
        }
        $tipo= ContNominaModels::where('codigo',$request->tprecibo)->first();
        
        $nombre_de_las_nominas = $request->nomina == "todos" ? "DG y DIGA" : ($nomina[$request->nomina]);
        $nameFecha =  $nombre_de_las_nominas.' - '.$tipo->descripcion.' Periodo '.trans('meses.' . $this->mes).'-'.$this->anio;
        
        usort($structData, function($a, $b) {
            // Comparar por cedula (numérico)
            $cedulaComparison = strcmp($a['cedula'], $b['cedula']);
            if ($cedulaComparison === 0) {
                // Si cedulas iguales, comparar por corte
                $corteComparison = strcmp($a['corte'], $b['corte']);
                if ($corteComparison === 0) {
                    // Si corte también igual, comparar por gerencia
                    return strcmp($a['gerencia'], $b['gerencia']);
                }
                return $corteComparison;
            }
            return $cedulaComparison;
        });

        return Excel::download(new ExportNominas($totalItems,$structData), 'Reporte de Pagos '.$nameFecha.'.csv');

       }catch(Exception $e){
        Session::flash('error',"Ocurrio un problema al procesar ". $e->getMessage());
        return back();
       }
    }
}

