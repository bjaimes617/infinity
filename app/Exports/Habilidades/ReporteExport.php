<?php

namespace App\Exports\Habilidades;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\Habilidades\Sheets\RespuestasFormExport;
use App\Exports\Habilidades\Sheets\RfExport;
use App\Exports\Habilidades\Sheets\InformeExport;

class ReporteExport implements WithMultipleSheets
{
    use Exportable;

    private $preguntas_eval;
    private $form;
    private $datas;
    private $preguntas_eval_rf;
    private $result;
    private $cantidad;
    private $resultadoPromedio;
    private $test_name;
    private $rango_evaluacion;
    private $nombre_completo;
    private $sede;
    private $position_name;
    private $cates;
    private $nota_final;
    private $headers_param;
    private $data_com;
    private $ex;

    public function __construct($preguntas_eval, $form, $datas, $preguntas_eval_rf,$result,$cantidad,$resultadoPromedio,$test_name,$rango_evaluacion,$nombre_completo,$sede,$position_name,$cates,$nota_final,$headers_param,$data_com,$ex) {
        $this->preguntas_eval = $preguntas_eval;
        $this->form = $form;
        $this->datas = $datas;
        $this->preguntas_eval_rf = $preguntas_eval_rf;
        $this->result = $result;
        $this->cantidad = $cantidad;
        $this->resultadoPromedio = $resultadoPromedio;
        $this->test_name = $test_name;
        $this->rango_evaluacion = $rango_evaluacion;
        $this->nombre_completo = $nombre_completo;
        $this->sede = $sede;
        $this->position_name = $position_name;
        $this->cates = $cates;
        $this->nota_final = $nota_final;
        $this->headers_param = $headers_param;
        $this->data_com = $data_com;
        $this->ex = $ex;
    }

    public function sheets(): array
    {
        return [
            'Respuestas de Formulario' => new RespuestasFormExport($this->preguntas_eval,$this->form), // Clase para el reporte de respuestas del formulario
            'RF' => new RfExport($this->preguntas_eval_rf,$this->datas,$this->result,$this->cantidad,$this->resultadoPromedio,$this->ex), // Clase para el reporte RF
            'E.D' => new InformeExport($this->test_name,$this->rango_evaluacion,$this->nombre_completo,$this->sede,$this->position_name,$this->cates,$this->nota_final,$this->headers_param,$this->data_com,$this->resultadoPromedio),
            // Agregar más hojas según sea necesario
        ];
    }
}