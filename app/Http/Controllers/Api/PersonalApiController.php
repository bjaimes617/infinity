<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\cursos\ParticipantesModel;

use App\Models\Apis\CondominioModel;

use App\Models\recibos\PeriodosModels;
use App\Models\recibos\RecibosNominaModel;
use App\Models\recibos\AsigDeducModel;
use App\Models\recibos\ContNominaModels;

use App\Models\personal\VisitantesModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\BancosModels;

use App\Models\constancias\ConstanciasModel;

use Luecano\NumeroALetras\NumeroALetras;

use App\Http\Controllers\PoliceEstructure;
use Carbon\Carbon;

class PersonalApiController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ValidatePersonal(Request $request) {

        $array = ["nacionalidad", "primer_nombre", "primer_apellido", "email", "fecha_nacimiento", "telefono_movil", "hijos", "fecha_ingreso", "nomina_id",
            "bancos_id", "cuenta_bancaria", "horario_entrada", "sede_laboral"];

        $banco = BancosModels::where('estatus_id', 1)->orderby('bancos', 'ASC')->pluck('bancos', 'id');

        $random_keys = array_rand($array, 3);
        $random_values = [];

        foreach ($random_keys as $key) {
            $random_values[] = $array[$key];
        }
        if (PersonalModel::where('cedula_empleado', $request->cedula)->whereNotIn('estatus_id', [2, 3, 5, 6])->exists()) {
            $autorizado = 200;
            $row = PersonalModel::Select("cedula_empleado", $random_values[0], $random_values[1], $random_values[2])->where('cedula_empleado', $request->cedula)->first();
        } else {
            $autorizado = 204;
            $row = false;
        }
        /// array de retorno 0 datos 1 aleatorios, 2 autoriza o no, 3 listado de bancos , 4 nominas
        return response()->json([$row, $random_values, $autorizado, $banco]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function CheckPersonalAutoservice(Request $request) {
        if (PersonalModel::where('cedula_empleado', $request->cedula)->whereNotIn('estatus_id', [2, 3, 5, 6])->exists()) {
            $autorizado = 200;
            $row = "Autorizado";
        } else {
            $autorizado = 204;
            $row = "No Autorizado";
        }
        return response()->json($autorizado);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $cedula) {

        $row = array();
        try {
            ///Buscamos si existe en la tabla principal
            if (PersonalModel::where('cedula_empleado', $cedula)->whereNotIn('estatus_id', [2, 3, 5, 6])->exists()) {
                $check = PersonalModel::select('cedula_empleado', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'area_trabajo_id', 'cargo_id', 'estatus_id')->where('cedula_empleado', $cedula)->first();
                $row["cedula"] = $check->cedula_empleado;
                $row["nombre"] = $check->primer_nombre . ' ' . $check->segundo_nombre . ' ' . $check->primer_apellido . ' ' . $check->segundo_apellido;
                $row["gerencia"] = $check->RelationAreas->nombre_campana;
                $row["cargo"] = $check->RelationCargos != null ? $check->RelationCargos->nombre_cargo : 'NO DISPONIBLE';
                $row["estatus"] = $check->RelationEstatus->estatus;
                $row["clasebg"] = "bg-success";
                $row["autorizado"] = "<p>EMPLEADO AUTORIZADO</p><p><i class='fas fa-check-circle'></i></p>";
                $autorizado = 200;

                $condominio = new CondominioModel();
                $condominio->cedula_empleado = $check->cedula_empleado;
                $condominio->primer_nombre = $check->primer_nombre;
                $condominio->primer_apellido = $check->primer_apellido;
                $condominio->empresa = "EMPLEADO";
                $condominio->sede = $request->sede;
                $condominio->save();

                $autorizado = 200;

                ///Buscamos si existe en la tabla temporal 
            } else if (ParticipantesModel::join('rec_cursos', 'rec_cparticipantes.rec_cursos_id', 'rec_cursos.id')
            ->where('cedula_empleado', $cedula)
            ->whereRaw('CURDATE() <= DATE_ADD(rec_cursos.finaliza, INTERVAL 5 DAY)')->exists()) {
                
                $check = ParticipantesModel::where('cedula_empleado', $cedula)->first();

                if (in_array($check->estatus_id, [12])) {
                    $row["cedula"] = $check->cedula_empleado;
                    $row["nombre"] = $check->primer_nombre . ' ' . $check->primer_apellido;
                    $row["gerencia"] = $check->RelationCurso->Relationskill->nombre_skill;
                    $row["cargo"]   = "Fecha Limite de Acceso como participante: ".carbon::create($check->RelationCurso->finaliza)->addDays(5)->format('d/m/Y');
                    $row["estatus"] = "ACTIVO";
                    $row["clasebg"] = "bg-info";
                    $row["autorizado"] = "<p>PARTICIPANTE A CURSO</p><p><i class='fas fa-check-circle'></i></p>";
                    $autorizado = 200;

                    $condominio = new CondominioModel();
                    $condominio->cedula_empleado = $check->cedula_empleado;
                    $condominio->primer_nombre = $check->primer_nombre;
                    $condominio->primer_apellido = $check->primer_apellido;
                    $condominio->empresa = "VISITANTE";
                    $condominio->sede = $request->sede;
                    $condominio->save();
                } else {
                    $autorizado = 204;
                }
            } else if (VisitantesModel::where('cedula_empleado', $cedula)->exists()) {

                $check = VisitantesModel::where('cedula_empleado', $cedula)->first();
                $row["cedula"] = $check->cedula_empleado;
                $row["nombre"] = $check->primer_nombre . ' ' . $check->primer_apellido;
                $row["gerencia"] = 'Directa Group';
                $row["cargo"] = "No Disponible";
                $row["estatus"] = "ACTIVO";
                $row["clasebg"] = "bg-info";
                $row["autorizado"] = "<p>VISITANTE AUTORIZADO</p><p><i class='fas fa-check-circle'></i></p>";
                $autorizado = 200;

                $condominio = new CondominioModel();
                $condominio->cedula_empleado = $check->cedula_empleado;
                $condominio->primer_nombre = $check->primer_nombre;
                $condominio->primer_apellido = $check->primer_apellido;
                $condominio->empresa = "VISITANTE";
                $condominio->sede = $request->sede;
                $condominio->save();
            } else {
                $autorizado = 204;
            }

            return response()->json($row, $autorizado);
        } catch (\Exception $ex) {
            $autorizado = 500;
            return response()->json($ex->getMessage(), $autorizado);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ResourcePayments(Request $request) {

        $cedula = PersonalModel::select('id', 'primer_nombre', 'primer_apellido', 'segundo_nombre', 'segundo_apellido', 'cedula_empleado', 'cargo_id', 'nomina_id')
            ->where('cedula_empleado', $request->cedula)->first();

        $dataCargo = $cedula->RelationCargos;
        $cargo = $dataCargo->nombre_cargo;
        $directivo = $dataCargo->directivo;

        $tipoPago = ContNominaModels::where('codigo', $request->tprecibo)->where('nomina_id', $cedula->nomina_id)->first();
        
        if ($tipoPago === null) {
            Return response()->json([
                        "active" => false,
                        "message" => "Tipo de Recibo no disponible."]);
        }

        switch ($tipoPago->type) {
            case "Q":
                $arrayBegin = carbon::createFromFormat('m/Y', $request->mes, 'UTC');
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
                $arrayBegin = carbon::createFromFormat('m/Y', $request->mes, 'UTC');
                $this->mes = $arrayBegin->copy()->format('F');
                $this->anio = $arrayBegin->copy()->format('Y');
                $this->fechainicio = $arrayBegin->copy()->StartOfMonth();
                $this->fechacorte = $arrayBegin->copy()->EndOfMonth();
                break;
            case "A":
                $arrayBegin = Carbon::createFromFormat('Y', $request->mes, 'UTC');
                $this->anio = $arrayBegin->copy()->format('Y');
                $this->fechainicio = $arrayBegin->copy()->StartofYear();
                $this->fechacorte = $arrayBegin->copy()->EndofYear();
                break;
        }

        switch ($request->corte) {
            case 1:
            case 2:
                if (PeriodosModels::where('corte', $request->corte)->where('rp_cont_nomina', $tipoPago->codigo)->where('mes', $this->mes)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->exists()) {

                    $sql = PeriodosModels::where('corte', $request->corte)->where('rp_cont_nomina', $tipoPago->codigo)->where('mes', $this->mes)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->first();

                    if ($sql->RelationRecibosNominas()->where('cedula', $cedula->cedula_empleado)->exists()) {
                        $rn = $sql->RelationRecibosNominas()->where('cedula', $cedula->cedula_empleado)->first();
                        $rn->load('RelationAsignaDeducs');

                        $asig = $rn->RelationAsignaDeducs()->select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                        ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                        ->groupby('rp_items_id')->orderBy('tipo', 'asc')
                                        ->orderBy('coditem', 'ASC')->get();

                        $fechacorte = $this->fechacorte;
                        $fechainicio = $this->fechainicio;
                        $activesummonto = false;
                        $tipocorte = $sql->corte;

                        if ($sql->active === 1) {
                            return response()->json(["active" => true,
                                        "cedula" => $cedula,
                                        "periodo" => $sql,
                                        "fechainicio" => $fechainicio,
                                        "fechacorte" => $fechacorte,
                                        "nomina" => $rn,
                                        "asig" => $asig,
                                        "cargo" => $cargo,
                                        "directivo" => $directivo,
                                        "tipocorte" => $tipocorte,
                                        "activesummonto" => $activesummonto]);
                        } else {
                            Return response()->json([
                                        "active" => false,
                                        "message" => "No se encuentra Disponible el periodo Seleccionado, Intente más tarde."]);
                        }
                    } else {
                        Return response()->json([
                                    "active" => false,
                                    "message" => "No se puede ubicar el recibo de pago de este personal"]);
                    }
                } else {
                    Return response()->json([
                                "active" => false,
                                "message" => "No Se puede ubicar la información sobre el recibo Consultado"]);
                }
                break;
            case 3:
            case 4:                
                switch (trim($tipoPago->codigo)) {
                    case"90": /// utilidades anuales
                        if (PeriodosModels::whereIn('corte', [1, 2, 4])->where('rp_cont_nomina', $tipoPago->codigo)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->exists()) {

                            $sqls = PeriodosModels::wherein('corte', [1, 2, 4])->where('rp_cont_nomina', $tipoPago->codigo)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->get();
                            $idperiodos = $sqls->pluck('id');
                            
                            ///nos traemos el ultimo recibo de pago para calcula
                            if (RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->exists()) {
                                $recibo = RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->get();
                                $idrecibo = $recibo->pluck('id');
                                $rn = $recibo->last();

                                $sql = PeriodosModels::find($rn->rp_periodo_id);

                                $asig = AsigDeducModel::select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                                ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                                ->wherein('rp_nomina_id', $idrecibo)->groupby('rp_items_id')
                                                ->orderBy('tipo', 'asc')->orderBy('coditem', 'ASC')->get();

                                $fechacorte = $this->fechacorte;
                                $fechainicio = $this->fechainicio;

                                $activesummonto = true;
                                $tipocorte = $request->corte;

                                if ($sql->active === 1) {
                                    return response()->json(["active" => true,
                                                "cedula" => $cedula,
                                                "periodo" => $sql,
                                                "fechainicio" => $fechainicio,
                                                "fechacorte" => $fechacorte,
                                                "nomina" => $rn,
                                                "asig" => $asig,
                                                "cargo" => $cargo,
                                                "directivo" => $directivo,
                                                "tipocorte" => $tipocorte,
                                                "activesummonto" => $activesummonto]);
                                } else {
                                    Return response()->json([
                                                "active" => false,
                                                "message" => "No se encuentra Disponible el periodo Seleccionado, Intente más tarde.",
                                                "data" => $sqls]);
                                }
                            } else {
                                Return response()->json([
                                            "active" => false,
                                            "message" => "No se puede ubicar el recibo de pago de este personal"]);
                            }
                        } else {
                            Return response()->json([
                                        "active" => false,
                                        "message" => "No Se puede ubicar la información sobre el recibo Consultado"]);
                        }
                        break;
                    case "90A"; ///anticipos de utilidades  sin importar los meses de carga                     
                        if (PeriodosModels::whereIn('corte', [1, 2, 4])->where('rp_cont_nomina', $tipoPago->codigo)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->where('active', 1)->exists()) {

                            $sqls = PeriodosModels::whereIn('corte', [1, 2, 4])->where('rp_cont_nomina', $tipoPago->codigo)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->where('active', 1)->get();
                            $idperiodos = $sqls->pluck('id');
                            if (RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->exists()) {

                                $datarecibo = array();
                                foreach ($sqls as $periodos) {

                                    $rn = $periodos->RelationRecibosNominas()->where('cedula', $cedula->cedula_empleado)->first();

                                    $datarecibo[$periodos->id]["fechainicio"] = Carbon::create($rn->fec_ini)->format('d/m/Y');
                                    $datarecibo[$periodos->id]["fechacorte"] = Carbon::create($rn->fec_fin)->format('d/m/Y');
                                    $datarecibo[$periodos->id]["id"] = $rn->id;

                                    $asig = AsigDeducModel::select('coditem', 'descripcion', DB::raw("sum(monto) as monto"), 'tipo', 'auxi_num', 'auxi_char')
                                                    ->join('rp_items', 'rp_asignaciones_deducciones.rp_items_id', 'rp_items.id')
                                                    ->where('rp_nomina_id', $rn->id)->groupby('rp_items_id')
                                                    ->orderBy('tipo', 'asc')->orderBy('coditem', 'ASC')->get();

                                    foreach ($asig as $key => $asignaciones) {
                                        $datarecibo[$periodos->id]["asignaciones"][$key]["descripcion"] = $asignaciones->coditem . " - " . strtoupper($asignaciones->descripcion);
                                        $datarecibo[$periodos->id]["asignaciones"][$key]["monto"] = $asignaciones->monto;
                                        $datarecibo[$periodos->id]["asignaciones"][$key]["tipo"] = $asignaciones->tipo;
                                    }
                                }
                                $activesummonto = true;
                                $tipocorte = $request->corte;

                                return response()->json(["active" => true,
                                            "cedula" => $cedula,
                                            "periodo" => $sqls->toArray(),
                                            "datarecibo" => $datarecibo,
                                            "cargo" => $cargo,
                                            "directivo" => $directivo,
                                            "tipocorte" => $tipocorte,
                                            "activesummonto" => $activesummonto]);
                            } else {
                                Return response()->json([
                                            "active" => false,
                                            "message" => "No se puede ubicar el recibo de pago de este personal"]);
                            }
                        } else {
                            Return response()->json([
                                        "active" => false,
                                        "message" => "No se encuentra Disponible el periodo Seleccionado, Intente más tarde.",]);
                        }
                        break;
                    default:
                        
                  //     return json_encode([$this->mes, $this->anio,"default",$tipoPago->codigo]);
                        
                        if (PeriodosModels::whereIn('corte', [1, 2, 4])->where('rp_cont_nomina', trim($tipoPago->codigo))->where('mes', $this->mes)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->exists()) {
                            $sqls = PeriodosModels::wherein('corte', [1, 2, 4])->where('rp_cont_nomina',trim($tipoPago->codigo))->where('mes', $this->mes)->where('anio', $this->anio)->where('tiponomina', $cedula->nomina_id)->get();

                            $idperiodos = $sqls->pluck('id');
                            ///nos traemos el ultimo recibo de pago para calcula
                            if (RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->exists()) {
                                $recibo = RecibosNominaModel::whereIn('rp_periodo_id', $idperiodos)->where('cedula', $cedula->cedula_empleado)->get();

                                $idrecibo = $recibo->pluck('id');
                                $rn = $recibo->last();

                                $sql = PeriodosModels::find($rn->rp_periodo_id);

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

                                if ($sql->active === 1) {
                                    return response()->json(["active" => true,
                                                "cedula" => $cedula,
                                                "periodo" => $sql,
                                                "fechainicio" => $fechainicio,
                                                "fechacorte" => $fechacorte,
                                                "nomina" => $rn,
                                                "asig" => $asig,
                                                "cargo" => $cargo,
                                                "directivo" => $directivo,
                                                "tipocorte" => $tipocorte,
                                                "activesummonto" => $activesummonto]);
                                } else {
                                    Return response()->json([
                                                "active" => false,
                                                "message" => "No se encuentra Disponible el periodo Seleccionado, Intente más tarde.",
                                    ]);
                                }
                            } else {
                                Return response()->json([
                                            "active" => false,
                                            "message" => "No se puede ubicar el recibo de pago de este personal"]);
                            }
                        } else {
                            Return response()->json([
                                        "active" => false,
                                        "message" => "No Se puede ubicar la información sobre el recibo Consultado"]);
                        }
                        break;
                }
                break;
        }//fin swich cortes
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    /** para generar las connstancias por primera vez */
    public function ConstanciasCreate(Request $request) {

        try {
            $cedula = PersonalModel::select('id', 'primer_nombre', 'primer_apellido', 'segundo_nombre', 'segundo_apellido',
                                    'cedula_empleado', 'cargo_id', 'nomina_id', 'fecha_ingreso', 'contrato_1', 'contrato_2', 'finalizacion_1', 'finalizacion_2', 'situacion_contractual')
                            ->where('cedula_empleado', $request->cedula)->first();

            if ($cedula->contrato_2 == null) {
                $fegaegreso = $cedula->finalizacion_1;
            } else {
                $fegaegreso = $cedula->finalizacion_2;
            }

            $gerenterrhh = PersonalModel::select('id', 'primer_nombre', 'primer_apellido', 'cedula_empleado',)->where('cargo_id', 71)->first();

            $nomina = $cedula->RelationNomina != null ? $cedula->RelationNomina->nombre_nomina : null;
            $cargo = $cedula->RelationCargos != null ? $cedula->RelationCargos->nombre_cargo : null;

            $monto = RecibosNominaModel::select('sueldo')
            ->join('rp_periodo', 'rp_nomina.rp_periodo_id', 'rp_periodo.id')
            ->where('rp_periodo.rp_cont_nomina', "01")
            ->where('cedula', $request->cedula)->get()->last();

            $formatter = new NumeroALetras();
            $montoletras = $formatter->toMoney($monto->sueldo, 2, 'BOLIVARES', 'CENTIMOS');
            $sueldo = [$monto->sueldo, $montoletras];

            $vigencia = carbon::now()->addMonths(3);

            switch ($request->constancia) {
                case 1:
                    if (ConstanciasModel::where('personal_id', $cedula->id)->where('tipo_contrato', $request->constancia)->whereDate('vigencia_fin', '>', now())->exists()) {
                        $cod = ConstanciasModel::where('personal_id', $cedula->id)->where('tipo_contrato', $request->constancia)->whereDate('vigencia_fin', '>', now())->first();
                        return response()->json(['Usted ya posee una constancia vigente de este tipo', $cod->serial], 200);
                    } else {
                        $constancia = new ConstanciasModel();
                        $constancia->serial = null;
                        $constancia->tipo_contrato = $request->constancia;
                        $constancia->dirigido_a = $request->dirigido;
                        $constancia->personal_id = $cedula->id;
                        $constancia->cargo_id = $cedula->cargo_id;
                        $constancia->fecha_ingreso = $cedula->fecha_ingreso;
                        $constancia->fecha_egreso = $fegaegreso;
                        $constancia->vigencia_fin = $vigencia->format('Y-m-d');
                        $constancia->sueldo = $monto->sueldo;
                        $constancia->gerente_id = $gerenterrhh->id;
                        $constancia->save();

                        $codserial = base64_encode($cedula->cedula_empleado . '|' . $constancia->id);
                        $constancia->serial = $codserial;
                        $constancia->save();

                        return response()->json([$cedula, $sueldo, $cargo, $gerenterrhh, $vigencia, $codserial, $fegaegreso], 200);
                    }
                    break;
                case 2:
                    if (ConstanciasModel::where('personal_id', $cedula->id)->where('tipo_contrato', $request->constancia)->whereDate('vigencia_fin', '>', now())->count() > 10) {
                        return response()->json(['Limite Excedido, debe esperar que finalice la vigencia de sus constancias', 10], 200);
                    } else {
                        $constancia = new ConstanciasModel();
                        $constancia->serial = null;
                        $constancia->tipo_contrato = $request->constancia;
                        $constancia->dirigido_a = $request->dirigido;
                        $constancia->personal_id = $cedula->id;
                        $constancia->cargo_id = $cedula->cargo_id;
                        $constancia->fecha_ingreso = $cedula->fecha_ingreso;
                        $constancia->fecha_egreso = $fegaegreso;
                        $constancia->vigencia_fin = $vigencia->format('Y-m-d');
                        $constancia->sueldo = $monto->sueldo;
                        $constancia->gerente_id = $gerenterrhh->id;
                        $constancia->save();

                        $codserial = base64_encode($cedula->cedula_empleado . '|' . $constancia->id);
                        $constancia->serial = $codserial;
                        $constancia->save();

                        return response()->json([$cedula, $sueldo, $cargo, $gerenterrhh, $vigencia, $codserial, $fegaegreso], 200);
                    }
                    break;
            }
        } catch (\Exception $e) {
            return response()->json(['Ocurrio un problema al intentar Generar La constancia' . $e->getMessage()], 200);
        }
    }

    /** para validar las constancias ya generadas */
    public function ConstanciaShow(Request $request) {
        try {
            if (ConstanciasModel::where("serial", $request->codigo)->exists()) {
                $cod = base64_decode($request->codigo, true);
                $subs = explode('|', $cod);

                $cedula = PersonalModel::select('id', 'primer_nombre', 'primer_apellido', 'segundo_nombre', 'segundo_apellido', 'estatus_id', 'fecha_egreso',
                                        'cedula_empleado', 'cargo_id', 'nomina_id', 'fecha_ingreso', 'contrato_1', 'contrato_2', 'finalizacion_1', 'finalizacion_2', 'situacion_contractual')
                                ->where('cedula_empleado', $subs[0])->first();

                $cons = ConstanciasModel::find($subs[1]);

                $formatter = new NumeroALetras();
                $montoletras = $formatter->toMoney($cons->sueldo, 2, 'BOLIVARES', 'CENTIMOS');

                $sueldo = [$cons->sueldo, $montoletras];

                $cargo = $cedula->RelationCargos != null ? $cedula->RelationCargos->nombre_cargo : null;
                $gerenterrhh = PersonalModel::select('id', 'primer_nombre', 'primer_apellido', 'cedula_empleado')->find($cons->gerente_id);

                $vigencia = $cons->vigencia_fin;
                $codserial = $request->codigo;
                $fegaegreso = $cons->fecha_egreso;

                return response()->json([$cedula, $sueldo, $cargo, $gerenterrhh, $vigencia, $codserial, $fegaegreso, $cons->dirigido_a]);
            } else {
                return response()->json(["Este Codigo de Validacion No es correcto"], 200);
            }
        } catch (\Throwable $e) {
            return response()->json(["No es Posible Procesar la validación del documento en este momento."], 200);
        }
    }

    public function ApiListadoGeneral(Request $request) {

        ini_set('max_execution_time', 300); // 300 segundos = 5 minutos

        $user = ENV('DEV_USER_CHANGE', false) ?
                \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                \Auth::user();

        //  dd($user,$user->tokenCan());
        $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
        $dato = PersonalModel::select('*');
        /*  DESCRIPCION DE NIVELES
         *   4: personal asignado y por skill autorizado
         *   3: Personal Por skill Autorizado
         *   2: Personal solo por asignados
         *   1: todos los registros
         */
        switch ($nivel) {
            case 4:
                ///Ubicamos los Id de todo el personal que tiene asignado
                $idPersonals = PoliceEstructure::PersonalAsignaded($user->cedula);
                /// extraemos los skills que estan activos en la plataforma
                $carteraPermisada = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillPermisado = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                $result = $dato->wherein('id', $idPersonals)
                                ->whereIn('cartera_id', $carteraPermisada)
                                ->whereIn('skill_id', $skillPermisado)
                                ->orderby('primer_nombre', 'ASC')->limit(1000)->get();
               
                break;
            case 3:
                $carterasautorize = $user->RelationPermisos()->groupby('cartera_id')->pluck('cartera_id');
                $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id');

                ///SELECTORES EN TODOS
                $result = $dato->WhereIn('skill_id', $skillsautorize)->orderby('primer_nombre', 'ASC')->limit(1000)->get();
                //$result = $dato->get();
                break;
            case 2:

                $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                /// area 0 | cartera 1 | skill 2 | personal asignado 3
                $skills = $carterasPersonal[2];
                $personal = $carterasPersonal[3];

                $result = $dato->whereIn('id', $personal)
                                ->wherein('skill_id', $skills)
                                ->orderby('primer_nombre', 'ASC')->limit(1000)->get();

                //   $result = $dato->get();
                break;
            case 1:
                ////4 SELECTORES EN TODOS
                $result = $dato->orderby('primer_nombre', 'ASC')->limit(1000)->get();
                break;
            default:
                ///retorna el array vacio, no posee permisos.
                return response()->json("Recursos no autorizados", 403);
                break;
        }
        
        foreach ($result as $datos) {
            
            $row["Registrado"]      = date('d/m/Y h:i:s', strtotime($datos->created_at));
            $row["Nacionalidad"]    = $datos->nacionalidad;
            $row["Cedula"]          = $datos->cedula_empleado;
            $row["RIF"]             = $datos->rif;
            $row["Primer Nombre"]   = $datos->primer_nombre;
            $row["Segundo Nombre"]  = $datos->segundo_nombre;
            $row["Primer Apellido"] = $datos->primer_apellido;
            $row["Segundo Apellido"] = $datos->segundo_apellido;
            $row["Email"]           = $datos->email;
            $row["Nivel Academico"] = $datos->nivel_academico;
            $row["Especialidad"]    = $datos->especialidad;
            $row["Fecha Nacimiento"]= date('d/m/Y', strtotime($datos->fecha_nacimiento));
            $row["Sexo"]            = $datos->sexo;
            $row["Tlf. Local"]      = $datos->telefono_casa;
            $row["Tlf. Movil"]      = $datos->telefono_movil;
            $row["Cant. hijos"]     = $datos->hijos;
            $row["Region"]          = $datos->RelationEstado != null ? $datos->RelationEstado->RelationRegion->nombre_region : null;
            $row["Estado"]          = $datos->RelationEstado != null ? $datos->RelationEstado->nombre_estado : null;
            $row["Ciudad"]          = $datos->RelationCiudad != null ? $datos->RelationCiudad->ciudad : null;
            $row["Sector"]          = $datos->sector;
            $row["Direccion"]       = $datos->direccion;
            
             if ($datos->htransporte != null) {
                switch ($datos->htransporte):
                    case 1 : $row["Utiliza Transporte"] = "AM";
                        break;
                    case 2 : $row["Utiliza Transporte"] = "PM";
                        break;
                    case 3 :$row["Utiliza Transporte"]  = "Ambos";
                        break;
                endswitch;
            } else {
                $row["Utiliza Transporte"] = "";
            }                       
            
            $row["Transporte"]      = $datos->htransporte;
            $row["Fecha Ingreso"]   = date('d/m/Y', strtotime($datos->fecha_ingreso));
            $row["Nomina"]          = $datos->RelationNomina != null ? $datos->RelationNomina->nombre_nomina : null;
            $row["Cargo"]           = $datos->RelationCargos != null ? $datos->RelationCargos->nombre_cargo : null;
            $row["Estatus Cargo"]   = $datos->estatuscargo;
            $row["CI Titular"]      = $datos->cedula_titular;
            $row["Nombre y Apellido del Titular"] = $datos->nombre_titular;
            $row["Banco"]                   = $datos->Relationbanco != null ? $datos->Relationbanco->bancos : "";
            $row["Nro Cuenta Bancaria"]     = strval($datos->cuenta_bancaria);
            $row["Todo Ticket Integral"]     = $datos->todoticket_integral == "1" ? "SI" : "NO";
            $row["Todo Ticket Alimentacion"] = $datos->todoticket_alimentacion == "1" ? "SI" : "NO";
            $row["Horario"]                  = $datos->horario_operativo;
            $row["Jornada Laboral"]         = $datos->jornada;
            $row["Turno"]                   = $datos->turno;
            $row["Esquema Libranzas"]       = $datos->RelationLibranzas != null ? $datos->RelationLibranzas->libranzas : null;
            $row["Hora de Entrada"]         = date('h:i a', strtotime($datos->hora_entrada));
            $row["Hora de Salida"]          = date('h:i:s', strtotime($datos->hora_salida));
            $row["Sede Laboral"]            = $datos->sede_laboral;
            $row["Gerente"]                 = $datos->RelationGerente != null ? $datos->RelationGerente->primer_nombre . ' ' . $datos->RelationGerente->primer_apellido : "N/A";
            $row["1er. Lider Inmediato"]    = $datos->RelationprimerLider != null ? $datos->RelationprimerLider->primer_nombre . ' ' . $datos->RelationprimerLider->primer_apellido : "N/A";
            $row["2do. Lider Inmediato"]    = $datos->RelationsegundoLider != null ? $datos->RelationsegundoLider->primer_nombre . ' ' . $datos->RelationsegundoLider->primer_apellido : "N/A";
            $row["Gerencia"]                = $datos->RelationAreas ? $datos->RelationAreas->nombre_campana : null;
            $row["Unidad/Campaña"]          = $datos->RelationCartera ? $datos->RelationCartera->nombre_cartera : null;
            $row["Segmento/skill"]          = $datos->Relationskill ? $datos->Relationskill->nombre_skill : null;
            $row["Estatus"]                 = $datos->RelationEstatus->estatus;
            $row["Firma 1er. Contrato"]         = date('d/m/Y', strtotime($datos->contrato_1));
            $row["Finalizacion 1er. Contrato"]  = date('d/m/Y', strtotime($datos->finalizacion_1));
            $row["Firma 2do. Contrato"]         = $datos->contrato_2 != null ? date('d/m/Y', strtotime($datos->contrato_2)): null;
            $row["Finalizacion 2do. Contrato"]  = $datos->finalizacion_2  != null ? date('d/m/Y', strtotime($datos->finalizacion_2)): null;
            $row["Tipo de Contrato"]            = $datos->situacion_contractual;
            $row["Fecha de Egreso"]             = $datos->fecha_egreso != null ? date('d/m/Y', strtotime($datos->fecha_egreso)) : null;
            $row["Motivo del Egreso"]           = $datos->motivo_egreso != null ? $datos->motivo_egreso : null;
            $row["Fotografia"]                  = $datos->image != null ?  "SI" : "NO";
            
            if ($datos->RelationCentroCosto == null) {
            $row["Cod. CECO"] = null;
            $row["Nombre CECOS"]  = null;
            $row["Porcentaje CECO"]  = null;
            } else {
                $cod = array();
                $ceco = array();
                $porcentaje = array();

                foreach ($datos->RelationCentroCosto()->get() as $centroasignados) {
                    
                    array_push($cod, $centroasignados->RelationCECO->cod_ceco);
                    array_push($ceco, $centroasignados->RelationCECO->nombre_ceco);
                    array_push($porcentaje, number_format($centroasignados->porcentaje));
                }
                $string = '|';
                $row["Cod. CECO"] = implode($string, $cod);
                $row["Nombre CECOS"] = implode($string, $ceco);
                $row["Porcentaje CECO"] = implode($string, $porcentaje);
            }
            
            $data[] = $row;
        }

        return response()->json($data, 200);
    }
}
