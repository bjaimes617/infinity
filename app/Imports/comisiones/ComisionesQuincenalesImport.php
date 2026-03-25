<?php

namespace App\Imports\comisiones;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use App\Models\personal\PersonalModel;
use App\Models\personal\SkillModels;
use App\Models\personal\BlacklistModel;
use App\Models\Comisiones\OperacionesComisiones;
use App\Models\Comisiones\LoteModel;
use App\Models\User;
use \jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use App\Exceptions\ValidateAditionals;
use Carbon\Carbon;

class ComisionesQuincenalesImport implements ToCollection, WithHeadingRow, WithValidation {

    use Importable,
        ActivityLogger;

    public function __construct($valores) {

        $this->gerencia_id = $valores[0];
        $this->campana_id = $valores[1];
        $this->skill_id = $valores[2];
        $this->tipo_periodo = $valores[3];
        $this->periodo = $valores[4];
        $this->mes = $valores[5];
        $this->anio = $valores[6];
        $this->lote_id = $valores[7];
        $this->path = $valores[8];
    }

    public function collection(Collection $rows) {

        $validateAdd = array();
        $montos = [];
        $update_register = false;
        $gerencia_asignada = [];
        $dis = [];
        $msj_dis = [];

        $cedulas_vistas = [];
        $duplicados = [];

        //VALIDACIONES ADICIONALES
        foreach ($rows as $row) {
            $cedula[] = trim($row['cedula']);
        }

        $date = Carbon::createFromFormat('m/Y', $this->mes . "/" . $this->anio);
        $monthInEnglish = $date->copy()->format('F');

        ///validaciones en caso de que se inente cargar un perioro que ya ha sido pagado
        if (!\Auth::user()->hasPermission('comisiones.demoradas')) {
            if (OperacionesComisiones::where('gerencia_id', $this->gerencia_id)
                            ->where('campana_id', $this->campana_id)->where('skill_id', $this->skill_id)
                            ->where('tipo_periodo', $this->tipo_periodo)->where('periodo', $this->periodo)
                            ->where('anio', $this->anio)->where('mes', $monthInEnglish)->where('incidencia', 0)->exists()) {

                $estatus_per = OperacionesComisiones::select('incidencia')->where('gerencia_id', $this->gerencia_id)
                                ->where('campana_id', $this->campana_id)->where('skill_id', $this->skill_id)
                                ->where('tipo_periodo', $this->tipo_periodo)->where('periodo', $this->periodo)
                                ->where('anio', $this->anio)->where('mes', $monthInEnglish)->first();
                $incidencia = $estatus_per->incidencia;
                if ($incidencia == 0) {
                    array_push($validateAdd, "Las comisiones para este periodo ya han sido canceladas");
                }
            }
        }

        ///validacion devuelve error si se inenta cargar otro archivo y el mismo lote esta auditado
        if (OperacionesComisiones::where('gerencia_id', $this->gerencia_id)->where('campana_id', $this->campana_id)->where('skill_id', $this->skill_id)
                        ->where('tipo_periodo', $this->tipo_periodo)->where('periodo', $this->periodo)->where('anio', $this->anio)
                        ->where('mes', $monthInEnglish)->where('incidencia', 3)->exists()) {

            $estatus_per = OperacionesComisiones::select('incidencia')->where('gerencia_id', $this->gerencia_id)
                            ->where('campana_id', $this->campana_id)->where('skill_id', $this->skill_id)
                            ->where('tipo_periodo', $this->tipo_periodo)->where('periodo', $this->periodo)
                            ->where('anio', $this->anio)->where('mes', $monthInEnglish)->first();
            $incidencia = $estatus_per->incidencia;
            if ($incidencia == 3) {
                array_push($validateAdd, "Las comisiones para este periodo ya han sido auditadas");
            }
        }

        //RECORRIDO DE CEDULAS DUPLICADAS
        foreach ($cedula as $key => $dups) {
            if (in_array($dups, $cedulas_vistas)) {
                // Si ya hemos visto esta cédula, la añadimos a la lista de duplicados
                if (!in_array($dups, $duplicados)) {
                    $duplicados[] = $dups;
                    array_push($validateAdd, "La cedula " . $dups . " se repite mas de una vez");
                }
            } else {
                // Si no, la añadimos a las cédulas vistas
                $cedulas_vistas[] = $dups;
            }

            /// VALIDACION DE TIEMPO DE EGRESO
            $personal = PersonalModel::select('fecha_egreso')->where('cedula_empleado', $dups)->whereNotNull('fecha_egreso')->first();
            if ($personal) {
                // Obtenemos la fecha de egreso y la normalizamos al inicio del día
                $fecha_egreso = Carbon::parse($personal->fecha_egreso)->startOfDay();

                // Calculamos la fecha límite sumando los días configurados (ejemplo: 50 días)
                $diasLimite = config('app.diasCargarComisionesEgresados', 50); // 50 por defecto si no está configurado
                $fecha_limite = $fecha_egreso->copy()->addDays($diasLimite)->startOfDay();
                // Fecha actual al inicio del día
                $fecha_actual = Carbon::now()->startOfDay();
                // Verificamos si la fecha actual está dentro del rango permitido
                if (!$fecha_actual->between($fecha_egreso, $fecha_limite)) {
                    array_push($validateAdd, "La persona " . $dups . " supera el lapso para carga de comisiones y variables");
                }
            }
        }

        ///VALIDACION DE MONTOS NEGATIVOS Y CREAMOS LOS ARREGLOS DE DATOS
        foreach ($rows as $row) {
            $objCeco = SkillModels::select('cod_ceco')->where('id', '=', $this->skill_id)->first();
            $objDatosPersonal = PersonalModel::select('id', 'nomina_id', 'cedula_empleado')->where('cedula_empleado', '=', trim($row['cedula']))->first();

            //SACAMOS LOS DATOS DEL TRABAJADOR PERSONAL_ID Y NOMINA_ID Y SU CEDULA
            $comision_usd_ar = str_replace(",", ".", $row['comision_usd']);
            $variable_usd_ar = str_replace(",", ".", $row['variable_usd']);
            $bonificacion_usd_ar = str_replace(",", ".", $row['bonificacion_usd']);
            $bono_nocturno_usd_ar = str_replace(",", ".", $row['bono_nocturno_usd']);
            $he_usd_com_ar = str_replace(",", ".", $row['he_usd']);
            $jornada_fin_de_semana_usd_ar = str_replace(",", ".", $row['jornada_fin_de_semana_usd']);
            $descuentos_usd_ar = str_replace(",", ".", $row['descuentos_usd']);

            $total_usd_ar = (floatval($comision_usd_ar) +
                    floatval($variable_usd_ar) +
                    floatval($bonificacion_usd_ar) +
                    floatval($bono_nocturno_usd_ar) +
                    floatval($he_usd_com_ar) +
                    floatval($jornada_fin_de_semana_usd_ar)) -
                    floatval($descuentos_usd_ar);

            $montos[] = [
                'cedula' => $objDatosPersonal->cedula_empleado,
                'personal_id' => $objDatosPersonal->id,
                'gerencia_id' => $this->gerencia_id,
                'campana_id' => $this->campana_id,
                'skill_id' => $this->skill_id,
                'tipo_periodo' => $this->tipo_periodo,
                'comision_usd' => $comision_usd_ar,
                'variable_usd' => $variable_usd_ar,
                'bonificacion_usd' => $bonificacion_usd_ar,
                'bono_noc_usd' => $bono_nocturno_usd_ar,
                'he_usd' => $he_usd_com_ar,
                'jornada_fin_usd' => $jornada_fin_de_semana_usd_ar,
                'descuentos_usd' => $descuentos_usd_ar,
                'total_usd_ar' => $total_usd_ar,
                'total_usd' => $total_usd_ar,
                'cantidad_ventas' => trim($row['kpi_comisionable']),
                'periodo' => $this->periodo,
                'mes' => $monthInEnglish,
                'nomina_id' => $objDatosPersonal->nomina_id,
                'ceco' => $objCeco->cod_ceco,
                'lote_id' => $this->lote_id,
                'anio' => $this->anio,
                'observaciones' => $row['observaciones']
            ];

            if ($total_usd_ar < 0) {
                array_push($validateAdd, 'El monto de descuento no puede ser mayor al total de bonificaciones, verifique la cedula: ' . $row['cedula']);
            }
        }

        ///RETORNO DE TODOS LOS ERRORES REFERENTE A LAS VALIDACIONES ADICIONALES
        if (!empty($validateAdd)) {           
            throw new ValidateAditionals($validateAdd);
        }       

        try {
            ///INICIAMOS REGISTRO O ACTUALIZACION
            foreach ($montos as $operacion) {
                if (OperacionesComisiones::where('cedula', trim($operacion['cedula']))->where('anio', $operacion['anio'])->where('mes', $operacion['mes'])->where('periodo', $operacion['periodo'])->where('tipo_periodo', $operacion['tipo_periodo'])->where('gerencia_id', $operacion['gerencia_id'])->where('campana_id', $operacion['campana_id'])->where('skill_id', $operacion['skill_id'])->exists()) {
                    $mensaje_log = "Actualizacion de Comisiones Quincenales";

                    //SACAMOS EL LOTE_ID PARA ACTUALIZAR EL REGISTRO Y NO ACTUALIZAR OTRO LOTE QUE CONTENGA LOS MISMOS DATOS

                    $bring_lote = OperacionesComisiones::select('lote_id')->where('cedula', $operacion['cedula'])
                                    ->where('anio', $operacion['anio'])
                                    ->where('mes', $operacion['mes'])
                                    ->where('periodo', $operacion['periodo'])
                                    ->where('tipo_periodo', $operacion['tipo_periodo'])
                                    ->where('gerencia_id', $operacion['gerencia_id'])
                                    ->where('campana_id', $operacion['campana_id'])
                                    ->where('skill_id', $operacion['skill_id'])->first();

                    $lote_ex = $bring_lote->lote_id;
                    $update_register = true;

                    $update_op = OperacionesComisiones::where('cedula', trim($operacion['cedula']))
                                    ->where('anio', $operacion['anio'])->where('mes', $operacion['mes'])
                                    ->where('periodo', $operacion['periodo'])
                                    ->where('tipo_periodo', $operacion['tipo_periodo'])
                                    ->where('gerencia_id', $operacion['gerencia_id'])
                                    ->where('campana_id', $operacion['campana_id'])
                                    ->where('skill_id', $operacion['skill_id'])
                                    ->where('lote_id', $lote_ex)->first();

                    $comision_usd_com = $operacion['comision_usd'];
                    $variable_usd_com = $operacion['variable_usd'];
                    $bonificacion_usd_com = $operacion['bonificacion_usd'];
                    $bono_nocturno_usd_com = $operacion['bono_noc_usd'];
                    $he_usd_com = $operacion['he_usd'];
                    $jornada_fin_de_semana_usd_com = $operacion['jornada_fin_usd'];
                    $descuentos_usd_com = $operacion['descuentos_usd'];

                    $update_op->comision_usd = trim($comision_usd_com);
                    $update_op->variable_usd = trim($variable_usd_com);
                    $update_op->bonificacion_usd = trim($bonificacion_usd_com);
                    $update_op->bono_noc_usd = trim($bono_nocturno_usd_com);
                    $update_op->he_usd = trim($he_usd_com);
                    $update_op->jornada_fin_usd = trim($jornada_fin_de_semana_usd_com);
                    $update_op->descuentos_usd = trim($descuentos_usd_com);
                    $update_op->cantidad_ventas = trim($operacion['cantidad_ventas']);
                    $update_op->observaciones = trim($operacion['observaciones']);

                    $comision_usd = str_replace(",", ".", $operacion['comision_usd']);
                    $variable_usd = str_replace(",", ".", $operacion['variable_usd']);
                    $bonificacion_usd = str_replace(",", ".", $operacion['bonificacion_usd']);
                    $bono_nocturno_usd = str_replace(",", ".", $operacion['bono_noc_usd']);
                    $he_usd = str_replace(",", ".", $operacion['he_usd']);
                    $jornada_fin_de_semana_usd = str_replace(",", ".", $operacion['jornada_fin_usd']);
                    $descuentos_usd = str_replace(",", ".", $operacion['descuentos_usd']);

                    $total_usd = (floatval($comision_usd) + floatval($variable_usd) + floatval($bonificacion_usd) + floatval($bono_nocturno_usd) + floatval($he_usd) + floatval($jornada_fin_de_semana_usd)) - floatval($descuentos_usd);

                    $update_op->total_usd = $total_usd;
                    $update_op->actualizado_por = \Auth::user()->username;
                    $update_op->save();
                } else {
                    $mensaje_log = "Registro de Comisiones Quincenales ";

                    $comision = new OperacionesComisiones();

                    $comision->cedula = $operacion['cedula'];
                    $comision->personal_id = $operacion['personal_id'];
                    $comision->gerencia_id = $operacion['gerencia_id'];
                    $comision->campana_id = $operacion['campana_id'];
                    $comision->skill_id = $operacion['skill_id'];
                    $comision->tipo_periodo = $operacion['tipo_periodo'];
                    $comision->comision_usd = $operacion['comision_usd'];
                    $comision->variable_usd = $operacion['variable_usd'];
                    $comision->bonificacion_usd = $operacion['bonificacion_usd'];
                    $comision->bono_noc_usd = $operacion['bono_noc_usd'];
                    $comision->he_usd = $operacion['he_usd'];
                    $comision->jornada_fin_usd = $operacion['jornada_fin_usd'];
                    $comision->descuentos_usd = $operacion['descuentos_usd'];
                    $comision->cantidad_ventas = $operacion['cantidad_ventas'];
                    $comision->observaciones = $operacion['observaciones'];

                    $comision->total_usd = $operacion['total_usd'];
                    $comision->periodo = $operacion['periodo'];
                    $comision->mes = $operacion['mes'];
                    $comision->nomina_id = $operacion['nomina_id'];
                    $comision->ceco = $operacion['ceco'];
                    $comision->lote_id = $operacion['lote_id'];
                    $comision->anio = $operacion['anio'];
                    //2 SIGNIFICA PAGO NO PROCESADO
                    $comision->incidencia = 2;
                    $comision->descripcion_incidencia = null;

                    //dd($this->periodo,$cedula,$this->mes);
                    $comision->gestionado_por = \Auth::user()->username;
                    $comision->actualizado_por = null;

                    $comision->save();

                    $objeto_comision = json_encode($comision);

                    ActivityLogger::activity($mensaje_log . " " . $comision->cedula, $objeto_comision);
                }
            }

            ///COMPROBACION DE EXISTENCIA ACTUALIZACION DE REGISTROS, DE SER TRUE, CAMBIAMOS EL ID DEL LOTE VIEJO POR LE NUEVO
            if ($update_register) {
                $operacion = OperacionesComisiones::select('lote_id')
                        ->where('anio', $this->anio)
                        ->where('mes', $monthInEnglish)
                        ->where('periodo', $this->periodo)
                        ->where('tipo_periodo', $this->tipo_periodo)
                        ->where('gerencia_id', $this->gerencia_id)
                        ->where('campana_id', $this->campana_id)
                        ->where('skill_id', $this->skill_id)
                        ->wherenotIn('lote_id', [$this->lote_id])
                        ->update(['lote_id' => $this->lote_id]);

                LoteModel::find($lote_ex)->delete();
                $objeto_comision = json_encode($update_op);

                ActivityLogger::activity($mensaje_log . " " . $update_op->cedula, $objeto_comision);
            }

            \Session::flash('success', 'Comisiones y/o variables cargadas exitosamente');
            return true;
        } catch (\Exception $e) {
            
            throw new ValidateAditionals([$e->getMessage()]);
        }
    }

    public function rules(): array {
        return [
            'cedula' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure(' La cedula del empleado es obligatoria.');
                } else {
                    if (!ctype_digit($value)) {
                        $onFailure('La Cedula de Identidad debe ser solo númerico');
                    }
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure(' La cedula ' . $value . ' no existe en el listado de personal.');
                    }
                }
            },
            'comision_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $comision_usd = floatval(str_replace(",", ".", $value));
                        if (trim($comision_usd) < 0) {
                            $onFailure('El monto de comision_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor comision_usd no es correcto');
                    }
                }
            },
            'variable_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $variable_usd = floatval(str_replace(",", ".", $value));
                        if (trim($variable_usd) < 0) {
                            $onFailure('El monto de variable_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor variable_usd no es correcto');
                    }
                }
            },
            'bonificacion_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $bonificacion_usd = floatval(str_replace(",", ".", $value));
                        if (trim($bonificacion_usd) < 0) {
                            $onFailure('El monto de bonificacion_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor bonificacion_usd no es correcto');
                    }
                }
            },
            'bono_nocturno_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $bono_nocturno_usd = floatval(str_replace(",", ".", $value));
                        if (trim($bono_nocturno_usd) < 0) {
                            $onFailure(' El monto de bono_nocturno_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor  bono_nocturno_usd no es correcto');
                    }
                }
            },
            'he_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $he_usd = floatval(str_replace(",", ".", $value));
                        if (trim($he_usd) < 0) {
                            $onFailure('El monto de he_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor he_usd no es correcto');
                    }
                }
            },
            'jornada_fin_de_semana_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $jornada_fin_de_semana_usd = floatval(str_replace(",", ".", $value));
                        if (trim($jornada_fin_de_semana_usd) < 0) {
                            $onFailure('El monto de jornada_fin_de_semana_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor jornada_fin_de_semana_usd no es correcto');
                    }
                }
            },
            'descuentos_usd' => function ($attribute, $value, $onFailure) {
                if ($value != "" || $value != null) {
                    if (is_numeric(str_replace(",", ".", $value))) {
                        $descuentos_usd = floatval(str_replace(",", ".", $value));
                        if (trim($descuentos_usd) < 0) {
                            $onFailure('El monto de descuentos_usd no puede ser negativo');
                        }
                    } else {
                        $onFailure('El valor descuentos_usd no es correcto');
                    }
                }
            }
        ];
    }
}
