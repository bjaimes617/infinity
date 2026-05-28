<?php

namespace App\Imports\personal;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\personal\PersonalModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\CargoModel;
use App\Models\personal\NominaModel;
use App\Models\personal\LibranzasModel;
use App\Models\personal\ZonaTransporte;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Models\personal\CentroCostoAsignadoModel;
use App\Models\personal\BancosModels;
use App\Models\personal\PermisosCarteraModel;
use App\Models\personal\EgresadosModel;
use App\Models\personal\HistoricoPersonalModel;
use App\Models\personal\SedeModel;
use App\Models\personal\MotivosEgresosModel;
use App\Models\asistencia\AsistenciaOperativaModel;
use App\Models\RegionesModel;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;
use App\Http\Controllers\PoliceEstructure;
use App\libs\ApiCheckautoservicios;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use App\Exceptions\ValidateAditionals;
use App\Models\personal\EstatusModel;

class UpdatePersonalImport implements ToCollection, WithHeadingRow, WithValidation {

    use Importable;
    use ActivityLogger;

    public function __construct() {

        $this->nacademicos = config('app.especialidad');
        $this->horariolab = config('app.horariolaboral');
        $this->contractual = config('app.contractual');
    }

    public function collection(Collection $rows) {
        
        $validateAdd = array();
        /// esta variable es para el mensaje de retoirno
        $cantidad = count($rows);

        if (\Auth::user()->hasPermission('personal.cecos.autorice')) {
            foreach ($rows as $key => $row) {
                $linea = $key + 1;
                if ($row["cod_ceco"] != "" && $row["porcentajes"] != "") {
                    $cods = explode("|", $row["cod_ceco"]);
                    $porcentajes = explode("|", $row["porcentajes"]);

                    // Validar si los Cecos son iguales a los porcentajes
                    if (count($cods) === count($porcentajes)) {
                        foreach ($cods as $codigosCecos) {
                            // Corroborar que el código CECO existe en la base de datos
                            if (!SkillModels::where('cod_ceco', $codigosCecos)->exists()) {
                                array_push($validateAdd, "Linea " . $linea . " El codigo del CECO " . $codigosCecos . " No existe");
                            }
                        }
                    } else {
                        array_push($validateAdd, "Linea " . $linea . "Cantidad de CECOS debe ser igual a La Cantidad de Porcentajes");
                    }
                } else {
                    continue;
                }
            }
        }

        foreach ($rows as $ke => $row) {
            $lin = $ke + 1;
            if (!empty($row['cuenta_bancaria'])) {
                if (PersonalModel::where('cuenta_bancaria', $row['cuenta_bancaria'])->where('cedula_empleado', '!=', $row['cedula_empleado'])->exists()) {
                    array_push($validateAdd, "Linea " . $lin . 'La cuenta Bancaria Ingresada ya se encuentra Registrada en otro Expediente');
                }
            }
        }

        if (!empty($validateAdd)) {
            throw new ValidateAditionals($validateAdd);
        }

        try {
            foreach ($rows as $row) {

                $objPersonal = PersonalModel::where('cedula_empleado', '=', trim($row['cedula_empleado']))->first();
                $mensaje_log = "Actualizacion Masiva de Expediente " . $row['cedula_empleado'];

                //ANTES DE ACTUALIZAR EL REGISTRO GUARDAMOS UN HISTORIAL DEL MISMO

                $historial = new HistoricoPersonalModel();

                $historial->cod_profit = $objPersonal->cod_profit;
                $historial->image = $objPersonal->image;
                $historial->nacionalidad = $objPersonal->nacionalidad;
                $historial->cedula_empleado = $objPersonal->cedula_empleado;
                $historial->primer_nombre = $objPersonal->primer_nombre;
                $historial->segundo_nombre = $objPersonal->segundo_nombre;
                $historial->primer_apellido = $objPersonal->primer_apellido;
                $historial->segundo_apellido = $objPersonal->segundo_apellido;
                $historial->email = $objPersonal->email;
                $historial->nivel_academico = $objPersonal->getrawOriginal('nivel_academico');
                $historial->especialidad = $objPersonal->especialidad;
                $historial->rif = $objPersonal->rif;
                $historial->fecha_nacimiento = $objPersonal->fecha_nacimiento;

                $historial->sexo = $objPersonal->sexo;
                $historial->telefono_casa = $objPersonal->telefono_casa;
                $historial->telefono_movil = $objPersonal->telefono_movil;
                $historial->hijos = $objPersonal->hijos;
                $historial->sector = $objPersonal->sector;

                $historial->estado_id = $objPersonal->estado_id;
                $historial->ciudad_id = $objPersonal->ciudad_id;

                $historial->activetransporte = $objPersonal->activetransporte;
                $historial->htransporte = $objPersonal->htransporte;
                $historial->zona_transporte_id = $objPersonal->zona_transporte_id;

                $historial->direccion = $objPersonal->direccion;
                $historial->fecha_ingreso = $objPersonal->fecha_ingreso;
                $historial->nomina_id = $objPersonal->nomina_id;
                $historial->cargo_id = $objPersonal->cargo_id;
                $historial->estatuscargo = $objPersonal->getrawOriginal('estatuscargo');

                $historial->nacionalidad_cuenta = $objPersonal->nacionalidad_cuenta;
                $historial->cedula_titular = $objPersonal->cedula_titular;
                $historial->nombre_titular = ucfirst(strtolower($objPersonal->nombre_titular));
                $historial->bancos_id = $objPersonal->bancos_id;
                $historial->cuenta_bancaria = $objPersonal->cuenta_bancaria;

                $historial->todoticket_integral = $objPersonal->todoticket_integral;
                $historial->todoticket_alimentacion = $objPersonal->todoticket_alimentacion;

                $historial->sede_laboral = $objPersonal->sede_laboral;
                $historial->horario_operativo = $objPersonal->horario_operativo;
                $historial->jornada = $objPersonal->getrawOriginal('jornada');

                $historial->turno = $objPersonal->turno;

                $historial->libranza_id = $objPersonal->libranza_id;
                $historial->horario_entrada = $objPersonal->horario_entrada;
                $historial->horario_salida = $objPersonal->horario_salida;

                $historial->gerente_area_id = $objPersonal->gerente_area_id;
                $historial->jefe_inmediato = $objPersonal->jefe_inmediato;
                $historial->jefe_inmediato_segundo = $objPersonal->jefe_inmediato_segundo;

                $historial->area_trabajo_id = $objPersonal->area_trabajo_id;
                $historial->cartera_id = $objPersonal->cartera_id;
                $historial->skill_id = $objPersonal->skill_id;

                $historial->contrato_1 = $objPersonal->contrato_1;
                $historial->finalizacion_1 = $objPersonal->finalizacion_2;
                $historial->contrato_2 = $objPersonal->contrato_2;
                $historial->finalizacion_1 = $objPersonal->finalizacion_2;

                $historial->situacion_contractual = $objPersonal->getrawOriginal('situacion_contractual');
                $historial->status_carnet = $objPersonal->status_carnet;

                $historial->fecha_egreso = $objPersonal->fecha_egreso;
                $historial->motivo_egreso = $objPersonal->motivo_egreso;
                $historial->estatus_id = $objPersonal->estatus_id;

                $historial->user_id = $objPersonal->user_id;

                //GUARDAMOS QUIEN ACTUALIZO EL REGISTRO
                $historial->modificado_por = \Auth::user()->username;
                //GUARDAMOS EL PERSONAL_ID PARA IDENTIFICAR DE QUIEN ES EL HISTORIAL
                $historial->personal_id = $objPersonal->id;
                $historial->save();

                if (!empty($row['cod_profit'])) {
                    $objPersonal->cod_profit = trim($row['cod_profit']);
                }
                if (!empty($row['nacionalidad'])) {
                    $objPersonal->nacionalidad = $row['nacionalidad'];
                }
                if (!empty($row['primer_nombre'])) {
                    $objPersonal->primer_nombre = ucfirst(strtolower(trim($row['primer_nombre'])));
                }
                if (!empty($row['segundo_nombre'])) {
                    $objPersonal->segundo_nombre = ucfirst(strtolower(trim($row['segundo_nombre'])));
                }
                if (!empty($row['primer_apellido'])) {
                    $objPersonal->primer_apellido = ucfirst(strtolower(trim($row['primer_apellido'])));
                }
                if (!empty($row['especialidad'])) {
                    $objPersonal->especialidad = ucfirst(strtolower(trim($row['especialidad'])));
                }
                if (!empty($row['segundo_apellido'])) {
                    $objPersonal->segundo_apellido = ucfirst(strtolower(trim($row['segundo_apellido'])));
                }
                if (!empty($row['rif'])) {
                    $objPersonal->rif = ucfirst(trim($row['rif']));
                }
                if (!empty($row['email'])) {
                    $objPersonal->email = trim($row['email']);
                }

                if (!empty($row['nivel_academico'])) {
                    $nivelAcademico = "";
                    switch ($row['nivel_academico']) {
                        case 'BACHILLER': $nivelAcademico = 1;
                            break;
                        case 'T.S.U': $nivelAcademico = 2;
                            break;
                        case 'LIC.': $nivelAcademico = 3;
                            break;
                        case 'ING.': $nivelAcademico = 4;
                            break;
                        case 'TECNICO MEDIO': $nivelAcademico = 6;
                            break;
                        case 'MAGISTER': $nivelAcademico = 7;
                            break;
                        case 'ESTUDIANTE': $nivelAcademico = 8;
                            break;
                        case 'OTRO': $nivelAcademico = 5;
                            break;
                    }
                    $objPersonal->nivel_academico = $nivelAcademico;
                }
                if (!empty($row['fecha_nacimiento'])) {
                    $objPersonal->fecha_nacimiento = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['fecha_nacimiento']))->format('Y-m-d');
                }

                if (!empty($row['sexo'])) {
                    $objPersonal->sexo = trim($row['sexo']);
                }
                if (!empty($row['telefono_casa'])) {
                    $objPersonal->telefono_casa = trim($row['telefono_casa']);
                }
                if (!empty($row['telefono_movil'])) {
                    $objPersonal->telefono_movil = trim($row['telefono_movil']);
                }
                if (!empty($row['hijos'])) {
                    $objPersonal->hijos = trim($row['hijos']);
                }
                if (!empty($row['sector'])) {
                    $objPersonal->sector = trim($row['sector']);
                }
                if (!empty($row['direccion'])) {
                    $objPersonal->direccion = trim($row['direccion']);
                }

                if (!empty($row['utiliza_transporte'])) {
                    if ($row['utiliza_transporte'] == "SI") {
                        $objPersonal->activetransporte = 1;

                        switch ($row['horario_transporte']) {
                            case 'AM': $horariotrans = 1;
                                break;
                            case 'PM': $horariotrans = 2;
                                break;
                            case 'AMBOS': $horariotrans = 3;
                                break;
                        }
                        $objPersonal->htransporte = $horariotrans;
                        $objPersonal->zona_transporte_id = ZonaTransporte::where('zonatransporte', '=', trim($row['zona_transporte']))->where('estatus_id', '=', 1)->first()->id;
                    } else {
                        $objPersonal->activetransporte = 0;
                        $objPersonal->htransporte = null;
                        $objPersonal->zona_transporte_id =56;
                    }
                }
                if (!empty($row['fecha_ingreso'])) {
                    $objPersonal->fecha_ingreso = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['fecha_ingreso']))->format('Y-m-d');
                }
                if (!empty($row['nomina'])) {
                    $objPersonal->nomina_id = NominaModel::where('nombre_nomina', '=', trim($row['nomina']))->where('estatus_id', '=', 1)->first()->id;
                }
                if (!empty($row['estatus_cargo'])) {
                    switch ($row["estatus_cargo"]):
                        case "Periodo de Prueba" : $estatuscargo = 1;
                            break;
                        case "Encargado" : $estatuscargo = 2;
                            break;
                        case "Titular" : $estatuscargo = 3;
                            break;
                    endswitch;
                    $objPersonal->estatuscargo = $estatuscargo;
                }
                if (!empty($row['cargo'])) {
                    $objPersonal->cargo_id = CargoModel::where('nombre_cargo', '=', trim($row['cargo']))->where('estatus_id', '=', 1)->first()->id;
                }
                if (!empty($row['horario_operativo'])) {
                    $horarioOperativo = array_search($row['horario_operativo'], config('app.horariolaboral'));

                    $objPersonal->horario_operativo = $horarioOperativo;
                }
                if (!empty($row['jornada'])) {
                    switch ($row['jornada']) {
                        case '8 Horas': $jornada = 1;
                            break;
                        case '6 Horas': $jornada = 2;
                            break;
                    }
                    $objPersonal->jornada = $jornada;
                }

                if (!empty($row['turno'])) {
                    $objPersonal->turno = trim($row['turno']);
                }
                if (!empty($row['nacionalidad_cuenta'])) {
                    $objPersonal->nacionalidad_cuenta = ucfirst($row['nacionalidad_cuenta']);
                }
                if (!empty($row['cedula_titular'])) {
                    $objPersonal->cedula_titular = $row['cedula_titular'];
                }
                if (!empty($row['nombre_titular'])) {
                    $objPersonal->nombre_titular = ucfirst(strtolower($row['nombre_titular']));
                }
                if (!empty($row['banco'])) {
                    $objPersonal->bancos_id = BancosModels::where('bancos', '=', trim($row['banco']))->first()->id;
                }
                if (!empty($row['cuenta_bancaria'])) {
                    $objPersonal->cuenta_bancaria = $row['cuenta_bancaria'];
                }

                if (!empty($row['libranza'])) {
                    $objPersonal->libranza_id = LibranzasModel::where('libranzas', '=', trim($row['libranza']))->first()->id;
                }
                if (!empty($row['horario_entrada'])) {
                    $objPersonal->horario_entrada = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['horario_entrada']))->format('H:i:s');
                }
                if (!empty($row['horario_salida'])) {
                    $objPersonal->horario_salida = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['horario_salida']))->format('H:i:s');
                }
                if (!empty($row['gerente_area'])) {
                    $objPersonal->gerente_area_id = PersonalModel::where('cedula_empleado', '=', trim($row['gerente_area']))->first()->id;
                }
                if (!empty($row['jefe_inmediato'])) {
                    $objPersonal->jefe_inmediato = PersonalModel::where('cedula_empleado', '=', trim($row['jefe_inmediato']))->first()->id;
                }
                if (!empty($row['jefe_inmediato_segundo'])) {
                    $objPersonal->jefe_inmediato_segundo = PersonalModel::where('cedula_empleado', '=', trim($row['jefe_inmediato_segundo']))->first()->id;
                }

                if (!empty($row['area_trabajo'])) {
                    $area = AreatrabajoModel::where('nombre_campana', '=', trim($row['area_trabajo']))->first()->id;
                    $objPersonal->area_trabajo_id = $area;
                }
                if (!empty($row['cartera'])) {
                    if( !isset( $area) ){
                        return back()->with('error', 'Ingrese La gerencia para poder asignar la cartera');
                    } else {
                        $cartera = CarteraModel::where('nombre_cartera', '=', trim($row['cartera']))->where('area_trabajo_id', $area)->first()->id;
                        $objPersonal->cartera_id = $cartera;
                    }
                }
                if (!empty($row['skill'])) {
                    if( !isset( $cartera) ){
                        return back()->with('error', 'Ingrese La cartera para poder asignar el skill');
                    } else {
                        $skill = SkillModels::where('nombre_skill', '=', trim($row['skill']))->where('cartera_id', $cartera)->first()->id;
                        $objPersonal->skill_id = $skill;
                    }
                }

                if (!empty($row['alimentacion'])) {

                    $objPersonal->todoticket_alimentacion = strtolower($row['alimentacion']) == "si" ? 1 : 0;
                }
                if (!empty($row['usuario_crm_portabilidad'])) {

                    $objPersonal->usuario_crm_portabilidad = ucwords(strtolower(trim($row['usuario_crm_portabilidad'])));
                }
                if (!empty($row['integral'])) {
                    $objPersonal->todoticket_integral = strtolower($row['integral']) == "si" ? 1 : 0;
                }

                if (!empty($row['contrato1'])) {

                    $valcontrato1 = Date::excelToDateTimeObject($row['contrato1'])->format('Y-m-d');
                    $objPersonal->contrato_1 = $valcontrato1;

                    $contrato1 = carbon::create($valcontrato1)->startOfDay()->addDays(config('app.contrato1') - 1);
                    $objPersonal->finalizacion_1 = $contrato1->copy()->format('Y-m-d');
                }
                if (!empty($row['contrato2'])) {

                    $valcontrato2 = Date::excelToDateTimeObject(trim($row['contrato2']))->format('Y-m-d');
                    $objPersonal->contrato_2 = $valcontrato2;

                    $contrato2 = carbon::create($valcontrato2)->startOfDay()->addDays($row['tiempocontrato2'] - 1);
                    $objPersonal->finalizacion_2 = $contrato2->copy()->format('Y-m-d');
                }

                if (!empty($row['nomina'])) {
                    $objPersonal->nomina_id = NominaModel::where('nombre_nomina', '=', trim($row['nomina']))->where('estatus_id', '=', 1)->first()->id;
                }

                if (!empty($row['situacion_contractual'])) {
           
                   // $situacionContractual = array_search(ucwords(strtolower($row['situacion_contractual'])),$this->contractual);                    
                    switch (ucwords(strtolower($row['situacion_contractual']))) {
                        case 'Contratado': $situacionContractual = 1;
                            break;
                        case 'Fijo': $situacionContractual = 2;
                            break;
                        case 'Freelance': $situacionContractual = 3;
                            break;
                        case 'Honorarios': $situacionContractual = 4;
                            break;
                        case 'Finalizado': $situacionContractual = 5;
                            break;
                        case 'Abandono': $situacionContractual = 6;
                            break;
                        case 'Pendiente Por Contrato': $situacionContractual = 7;
                            break;
                        case "Pendiente Por Documentacion" : $situacionContractual = 9;
                            break;
                    }                    
                    $objPersonal->situacion_contractual = $situacionContractual;
                }
                if (!empty($row['estatus_carnet'])) {
                    switch ($row['estatus_carnet']) {
                        case 'Impreso': $estatusCarnet = 1;
                            break;
                        case 'Extraviado': $estatusCarnet = 2;
                            break;
                        case 'No Impreso': $estatusCarnet = 3;
                            break;
                    }
                    $objPersonal->status_carnet = $estatusCarnet;
                }
                if ($row['sede_laboral'] != "") {
                    $objPersonal->sede_laboral = SedeModel::where('active', 1)->where('nombre_sede', trim($row['sede_laboral']))->first()->nombre_sede;
                }
                if (!empty($row['estado'])) {
                    $objPersonal->estado_id = in_array($objPersonal->sede_laboral, ['STV', 'ATENAS']) ? EstadosModel::where('nombre_estado', 'like', '%' . $row['estado'] . '%')->first()->id : 3;
                }
                if (!empty($row['ciudad'])) {
                    $objPersonal->ciudad_id = in_array($objPersonal->sede_laboral, ['STV', 'ATENAS']) ? CiudadesModel::where('ciudad', 'like', '%' . $row['ciudad'] . '%')->first()->id : 149;
                }
                
                if (!empty($row['estatus']) && !empty($row['fecha_egreso']) && !empty($row['motivo_egreso'])) {
                        $objPersonal->estatus_id = EstatusModel::where('estatus', 'like', '%' . $row['estatus'] . '%')->first()->id;
                        $objPersonal->fecha_egreso = Date::excelToDateTimeObject($row['fecha_egreso'])->format('Y-m-d');
                        $objPersonal->motivo_egreso = ucwords(strtolower($row['motivo_egreso']));
                        if (ENV('ACTIVE_API_AUTOSERVICIOS', false)) {
                            $bashurl = "URL_DISBLED_AUTOSERVICIOS";
                            $api = new ApiCheckautoservicios($bashurl, $objPersonal->cedula_empleado);
                            $rs = $api->CheckInfinityPersonal();
                        }
                }

            
               if (\Auth::user()->hasPermission('personal.cecos.autorice')) {                 
                    /// Tengo permisos para editar CECOS
                    if ($row["cod_ceco"] != "" && $row["porcentajes"] != "" || $row["cod_ceco"] != null && $row["porcentajes"] != null) {
                         ///columnas tienen datos   
                        $cods = explode("|", $row["cod_ceco"]);
                        $porcentajes = explode("|", $row["porcentajes"]);                                
                        CentroCostoAsignadoModel::where('personal_id', $objPersonal->id)->delete();
                        foreach ( $cods as $llave => $codigosCecos) {                     
                            switch ((string) $codigosCecos) {
                                case "30540":
                                    $aplicar = 21;
                                    $cecocarcartera = $objPersonal->cartera_id;
                                    break;
                                case "00001":                                
                                    $aplicar = 19;
                                    $cecocarcartera = $objPersonal->cartera_id;
                                    break;
                                case "30574":
                                    $aplicar = 20;
                                    $cecocarcartera = $objPersonal->cartera_id;
                                    break;
                                default:
                                    $ceco_skill = SkillModels::where('cod_ceco', $codigosCecos)->first();
                                    $aplicar = $ceco_skill->id;
                                    $cecocarcartera = $ceco_skill->cartera_id;
                                    break;
                            }
                            $cecos = new CentroCostoAsignadoModel();
                            $cecos->skill_id = $aplicar;
                            $cecos->cartera_id = $cecocarcartera;
                            $cecos->personal_id = $objPersonal->id;
                            $cecos->porcentaje = $porcentajes[$llave];
                            $cecos->save();
                        }
                    } else {                   
                        $verifiSkill = SkillModels::find($objPersonal->skill_id);                  
                        if($verifiSkill->cod_ceco != "00000" || $verifiSkill->cod_ceco == null){
                            CentroCostoAsignadoModel::where('personal_id', $objPersonal->id)->delete();
                            $areas = new CentroCostoAsignadoModel();
                            $areas->personal_id = $objPersonal->id;
                            $areas->cartera_id  = $objPersonal->cartera_id;
                            $areas->skill_id    = $objPersonal->skill_id;
                            $areas->porcentaje  = null;
                            $areas->save();  
                        } 
                    }
               } else {
                /// no tengo permisos para editar CEcos                   
                    $verifiSkill = SkillModels::find($objPersonal->skill_id);                  
                        if($verifiSkill->cod_ceco != "00000" || $verifiSkill->cod_ceco == null){
                            CentroCostoAsignadoModel::where('personal_id', $objPersonal->id)->delete();
                            $areas = new CentroCostoAsignadoModel();
                            $areas->personal_id = $objPersonal->id;
                            $areas->cartera_id  = $objPersonal->cartera_id;
                            $areas->skill_id    = $objPersonal->skill_id;
                            $areas->porcentaje  = null;
                            $areas->save();  
                        }                    
               }

                $nuevos_valores = [];
                $valores_alt = [];

                if($objPersonal->fecha_egreso != null && $objPersonal->estatus_id == 1){
                    $objPersonal->fecha_egreso = null;                 
                    $objPersonal->motivo_egreso = null;                 
                }
                
                // Verificar cambios realizados
                $changedFields = $objPersonal->getDirty(); // Obtiene los campos que fueron modificados
                // Antes de guardar, puedes inspeccionar qué ha cambiado
                if (!empty($changedFields)) {
                    foreach ($changedFields as $field => $newValue) {
                        $oldValue = $oldValues[$field] ?? null;
                        $nuevos_valores[$field] = $newValue;
                        $valores_alt[] = $field;
                    }
                }

                // Convertir el array a JSON
                $cambiosJson = json_encode($nuevos_valores);
                $valores_alterados = json_encode($valores_alt);
                $historial->campos_alt = $valores_alterados;
                $historial->valores_nuevos = $cambiosJson;
                $historial->save();

                $objPersonal->save();
                $objeto_personal = json_encode([$objPersonal,$row]);

                ActivityLogger::activity($mensaje_log, $objeto_personal);
            }
            \Session::flash('success', 'Un total de ' . $cantidad . ' Expedientes fueron actualizados exitosamente, por favor valide los datos.');
            return true;
        } catch (\Exception $e) {            
            throw new ValidateAditionals([$e->getMessage() . ' en Linea ' . $e->getLine()]);
        }
    }

    public function rules(): array {
        return [
            'nacionalidad' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!in_array($value, ["V", "E", "P"])) {
                        $onFailure('la nacionalidad ingresada no existe');
                    }
                }
            },
             'nacionalidad_cuenta' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!in_array($value, ["V", "E", "P"])) {
                        $onFailure('la nacionalidad del Titular de Cuenta no existe');
                    }
                }
            },
            'cedula_empleado' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (BlacklistModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('Esta Persona Se encuentra en Lista Negra. No se puede actualizar.');
                    } else if (!ctype_digit($value)) {
                        $onFailure('La Cedula de Identidad debe ser solo númerico');
                    }
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula no existe en el listado de personal.');
                    }

                    if (PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $objPersonal = PersonalModel::where('cedula_empleado', $value)->first();
                        $situacion = $objPersonal->getrawOriginal('situacion_contractual');
                        // SI LA SITUACION CONTRACTUAL NO ES FREELANCE HACEMOS LA COMPROBACION DE LOS 90 DIAS
                        if ($situacion != 3) {
                            if ($objPersonal->fecha_egreso != null) {
                                $fecha1 = Carbon::today();
                                $fecha2 = Carbon::parse($objPersonal->fecha_egreso);
                                ///validamos cuantos dias han pasado desde que fue egresado
                                $diferencia_en_dias = $fecha1->diffInDays($fecha2);
                                if(!\Auth::user()->hasPermission(['personal.update.blockregistred'])){
                                    if ($diferencia_en_dias <= config('app.diaspararecontratar')) {
                                        $onFailure('Esta persona tiene menos de 90 Días de Egresado.');
                                    }
                                }
                               
                            }
                        }
                    }
                }
            },
            'nivel_academico' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    //$nivelAcademico = array('BACHILLER', 'T.S.U', 'LIC.', 'ING.', 'TECNICO MEDIO', 'MAGISTER', 'OTRO');
                    if (!in_array(trim($value), config('app.especialidad'))) {
                        $onFailure('El nivel academico del empleado es invalido.');
                    }
                }
            },
            'sexo' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    $sexo = array('M', 'F');
                    if (!in_array(trim($value), $sexo)) {
                        $onFailure('El sexo del empleado es invalido.');
                    }
                }
            },
            'zona_transporte' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!ZonaTransporte::where('zonatransporte', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                        $onFailure('La zona de transporte ingresada no existe.');
                    }
                }
            },
            'nomina' => function ($attribute, $value, $onFailure) {
                //dd(trim($value),!NominaModel::where('nombre_nomina', '=', trim($value))->where('estatus_id', '=', 1)->exists());
                if (trim($value) != "") {
                    if (!NominaModel::where('nombre_nomina', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                        $onFailure('La nomina ingresada no existe.');
                    }
                }
            },
            'cargo' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!CargoModel::where('nombre_cargo', 'like', '%' . trim($value) . '%')->where('estatus_id', '=', 1)->exists()) {
                        $onFailure('El Cargo ingresado no existe.');
                    }
                }
            },
            'banco' => function ($attribute, $value, $onFailure) {
                //dd($value,strlen($value));
                if (trim($value) != "") {
                    if (!BancosModels::where('bancos', $value)->exists()) {
                        $onFailure('El nombre del banco no existe.');
                    }
                }
            },
            'cuenta_bancaria' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (strlen($value) < 20 && strlen($value) != 0) {
                        $onFailure('La cuenta Bancaria debe tener una longitud de 20 Digitos');
                    }
                }
            },
            'horario_operativo' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!in_array(trim($value), config('app.horariolaboral'))) {
                        $onFailure('El horario operativo del empleado es invalido.');
                    }
                }
            },
            'jornada' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    $jornada = array('8 Horas', '6 Horas');
                    if (!in_array(trim($value), $jornada)) {
                        $onFailure('La jornada del empleado es invalida.');
                    }
                }
            },
            'turno' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    $turno = array('AM', 'PM');
                    if (!in_array(trim($value), $turno)) {
                        $onFailure('El turno del empleado es invalido.');
                    }
                }
            },
            'libranza' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!LibranzasModel::where('libranzas', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                        $onFailure('La libranza ingresada no existe.');
                    }
                }
            },
            'gerente_area' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {

                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula del gerente del area ingresado no existe.');
                    } else {
                        if (!in_array(PersonalModel::where('cedula_empleado', '=', trim($value))->first()->cargo_id, [1, 2, 3, 59, 60, 61, 62, 63, 64, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 90, 91])) {
                            $onFailure('El numero de cedula ingresado no corresponde al de un gerente de area.');
                        }
                    }
                }
            },
            'jefe_inmediato' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula del jefe inmediato ingresado no existe.');
                    }
                }
            },
            'jefe_inmediato_segundo' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula del jefe inmediato segundo ingresado no existe.');
                    }
                }
            },
            'area_trabajo' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {

                    if (!AreatrabajoModel::where('nombre_campana', '=', trim($value))->exists()) {
                        $onFailure('La Gerencia ingresada no existe.');
                    }

                    $user = ENV('DEV_USER_CHANGE', false) ?
                            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                            \Auth::user();

                    $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
                    switch ($nivel) {
                        case 4:
                        case 3:
                            ///skills autorizados
                            $areas = $user->RelationPermisos()->groupby('area_trabajo_id')->pluck('area_trabajo_id')->toArray();
                            if (!in_array(AreatrabajoModel::where('nombre_campana', '=', trim($value))->where('estatus_id', '=', 1)->first()->id, $areas)) {
                                $onFailure('No cuenta con los permisos para acceder a esta Gerencia.');
                            }
                            break;
                        case 2:
                            ///skills del personal asignado
                            $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                            /// area 0 | cartera 1 | skill 2 | personal asignado 3
                            $area = $carterasPersonal[0];
                            $skills = $carterasPersonal[2];
                            $personal = $carterasPersonal[3];

                            $autorizados = $area->toArray();
                            if (!in_array(AreatrabajoModel::where('nombre_campana', '=', trim($value))->where('estatus_id', '=', 1)->first()->i, $autorizados)) {
                                $onFailure('No cuenta con los permisos para acceder a esta Gerencia.');
                            }
                            break;
                    }
                }
            },
            'cartera' => function ($attribute, $value, $onFailure) {

                if ($value != null) {
                    if (!CarteraModel::where('nombre_cartera', '=', trim($value))->where('estatus_id', '=', 1)->exists()) {
                        $onFailure('La Unidad o Campaña ingresada no existe.');
                    }
                }
            },
            'skill' => function ($attribute, $value, $onFailure) {
                if ($value != null) {

                    if (!SkillModels::where('nombre_skill', '=', trim($value))->where('active', '=', 1)->exists()) {
                        $onFailure('El Segmento o Skill ingresado no Existe');
                    }

                    $user = ENV('DEV_USER_CHANGE', false) ?
                            \Auth::loginUsingId(ENV('OTHER_USER_ID'), true) :
                            \Auth::user();

                    $nivel = PoliceEstructure::VerifiedNivelPermissions($user);
                    switch ($nivel) {
                        case 4:
                        case 3:
                            ///skills autorizados
                            $skillsautorize = $user->RelationPermisos()->groupby('skill_id')->pluck('skill_id')->toArray();
                            if (!in_array(SkillModels::where('nombre_skill', '=', ($value))->where('active', '=', 1)->first()->id, $skillsautorize)) {
                                $onFailure('Usted no tiene asignado el skill/segmento para actualizar el registro');
                            }
                            break;
                        case 2:
                            ///skills del personal asignado
                            $carterasPersonal = PoliceEstructure::PersonalForEstructure($user);
                            /// area 0 | cartera 1 | skill 2 | personal asignado 3
                            $skills = $carterasPersonal[2];
                            $autorizados = $skills->toArray();
                            $personal = $carterasPersonal[3];
                            if (!in_array(SkillModels::where('nombre_skill', '=', trim($value))->where('active', '=', 1)->first()->id, $autorizados)) {
                                $onFailure('Usted no tiene asignado el skill/segmento para actualizar el registro');
                            }
                            break;
                    }
                }
            },
            'situacion_contractual' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {                      
                    if (!in_array(ucwords(strtolower($value)), $this->contractual)) {
                        $onFailure('La situacion contractual ingresada es invalida.');
                    }
                }
            },
            'sede_laboral' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!SedeModel::where('active', 1)->where('nombre_sede', trim($value))->pluck('nombre_sede')) {
                        $onFailure('La sede laboral es invalida.');
                    }
                }
            },
            'estado' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!EstadosModel::where('nombre_estado', '=', strtoupper(ucfirst($value)))->where('active', '=', 1)->exists()) {
                        $onFailure('El estado no Existe');
                    }
                }
            },
            'ciudad' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    if (!CiudadesModel::where('ciudad', '=', strtoupper(ucfirst($value)))->exists()) {
                        $onFailure('La ciudad Ingresada no Existe');
                    }
                }
            },
            'contrato1' => function ($attribute, $value, $onFailure) {
                if ($value != null) {
                    if (!Date::excelToDateTimeObject(trim($value))) {
                        $onFailure('El Formato del Contrato 1 no es correcta ');
                    }
                }
            },
             'fecha_egreso' => function ($attribute, $value, $onFailure) {
                if ($value != null) {
                    if (!Date::excelToDateTimeObject(trim($value))) {
                        $onFailure('El Formato de la fecha de egreso no es correcta ');
                    }
                }
            },
            'estatus' => function ($attribute, $value, $onFailure) {
                if ($value != null) {
                     if (!EstatusModel::where('estatus', 'LIKE', '%' . $value . '%')->exists()) {
                        $onFailure('El estatus ingresado no es Correcto');
                    }
                }
            },
             'motivo_egreso' => function ($attribute, $value, $onFailure) {
                if ($value != null) {
                     if (!MotivosEgresosModel::where('descripcion', 'LIKE', '%' . $value . '%')->exists()) {
                        $onFailure('El motivo de egreso no es Correcto');
                    }
                }
            },
            '*.contrato2' => ['nullable', function ($attribute, $value, $onFailure) {
                    if ($value !== null) {
                        try {
                            Date::excelToDateTimeObject(trim($value));
                        } catch (\Exception $e) {
                            $onFailure('El Formato del Contrato 2 no es correcto.');
                        }
                    }
                }],
            'tiempocontrato2' => ['nullable', 'required_with:*.contrato2', 'in:90,60,120,180,240,270'],
            'estatus_carnet' => function ($attribute, $value, $onFailure) {
                if (trim($value) != "") {
                    $estatus = array('Impreso', 'Extraviado', 'No Impreso');
                    if (!in_array(trim($value), $estatus)) {
                        $onFailure('El estatus del carnet es invalido.');
                    }
                }
            }
        ];
    }
}
