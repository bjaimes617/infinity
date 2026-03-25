<?php

namespace App\Exports\Evaluaciones;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithChunkOffset;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\evaluacion\IndicadorModel;
use App\Models\evaluacion\SegmentoModel;
use App\Models\personal\PersonalModel;
use App\Models\personal\CentroCostoAsignadoModel;

class IndicadorExport implements ShouldAutoSize, FromCollection, WithHeadings, WithChunkReading, WithStyles
{
    private $valores;

    public function __construct($valores) {

        $this->area_trabajo = $valores[0];
        $this->carteras = $valores[1];
        $this->skill_asig = $valores[2];
        $this->cargo_eval = $valores[3];
        $this->tipo_periodo = $valores[4];

    }

    public function chunkSize(): int {
        return 3000;
    }

    public function startRow(): int {
        return 2;
    }


    public function collection()
    {

        $personal_auth = PersonalModel::select('id')->where('user_id',\Auth::user()->id)->first();

           $auth_id = $personal_auth->id;

        //OBTENEMOS TODAS LAS CEDULAS Y NOMBRES Y APELLIDOS DE LA PERSONA SEGUN EL SKILL Y CARGO SELECCIONADO
            
           if(\Auth::user()->hasRole('admin') || \Auth::user()->hasPermission('indicadores.carteras')){

            if($this->carteras == 53 && $this->area_trabajo == 2){
                
               $personal_ceco = CentroCostoAsignadoModel::select('personal_id')->where('skill_id',$this->skill_asig)->get()->toArray();

               $cedulas = PersonalModel::select('cedula_empleado','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido')
           ->whereIn('id',$personal_ceco)
           ->where('cargo_id',$this->cargo_eval)->where('estatus_id',1)->get();

            }else{

        $cedulas = PersonalModel::select('cedula_empleado','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido')
           ->where('area_trabajo_id',$this->area_trabajo)
           ->where('cartera_id',$this->carteras)->where('skill_id',$this->skill_asig)
           ->where('cargo_id',$this->cargo_eval)->where('estatus_id',1)->get();

            }

           }else{

            if($this->carteras == 53 && $this->area_trabajo == 2){
                
                $personal_ceco = CentroCostoAsignadoModel::select('personal_id')->where('skill_id',$this->skill_asig)->get()->toArray();
 
                $cedulas = PersonalModel::select('cedula_empleado','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido')
            ->whereIn('id',$personal_ceco)
            ->where('cargo_id',$this->cargo_eval)->where('estatus_id',1)
            ->where('jefe_inmediato',$auth_id)
            ->get();
 
             }else{

            $cedulas = PersonalModel::select('cedula_empleado','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido')
            ->where('area_trabajo_id',$this->area_trabajo)
            ->where('cartera_id',$this->carteras)->where('skill_id',$this->skill_asig)
            ->where('cargo_id',$this->cargo_eval)->where('estatus_id',1)
            ->where('jefe_inmediato',$auth_id)
            ->get();
             }

           }

        //BUSCAMOS EL ID DEL SEGEMENTO PARA PODER UBICAR LOS INDICADORES

        $segment_search = SegmentoModel::select('id')->where('area_trabajo_id',$this->area_trabajo)
        ->where('cartera_id',$this->carteras)->where('skill_id',$this->skill_asig)
        ->where('cargo_id',$this->cargo_eval)->where('tipo_periodo',$this->tipo_periodo)->first();

        $segmento_id = $segment_search->id;

        //BUSCAMOS LOS INDICADORES DEL SEGMENTO

        $indicadores = IndicadorModel::select('indicador','meta','tipo')->where('eval_segmentos_id',$segmento_id)->get();

        $data = [];

        foreach ($cedulas as $cedula) {
            $row = [];

            $row[] = $cedula->cedula_empleado;
            $row[] = $cedula->primer_nombre." ".$cedula->segundo_nombre." ".$cedula->primer_apellido." ".$cedula->segundo_apellido;
 
            foreach ($indicadores as $indicador) {

                $row[] = $indicador->indicador; // Indicador
                if($indicador->tipo == 1){
                $row[] = $indicador->meta."%"; // meta
                }else{
                $row[] = $indicador->meta;    
                }
                $row[] = ''; // Puntaje que se ingresará posteriormente
            }
            $data[] = $row;

        }
         
        return new Collection($data);
    }

    public function headings(): array
    {
        $headings = ['CEDULA'];
        $headings[] = 'NOMBRE';

        //CANTIDAD DE INDICADORES DEL SEGMENTO
        
         //BUSCAMOS EL ID DEL SEGEMENTO PARA PODER UBICAR LOS INDICADORES

         $segment_search = SegmentoModel::select('id')->where('area_trabajo_id',$this->area_trabajo)
         ->where('cartera_id',$this->carteras)->where('skill_id',$this->skill_asig)
         ->where('cargo_id',$this->cargo_eval)->where('tipo_periodo',$this->tipo_periodo)->first();
 
         $segmento_id = $segment_search->id;

         $indicador_count = IndicadorModel::where('eval_segmentos_id',$segmento_id)->count();

        for($i=1; $i<=$indicador_count; $i++){

            $headings[] = 'INDICADOR' . $i;
            $headings[] = 'META' . $i; // O lo que sea adecuado
            $headings[] = 'PUNTAJE' . $i; // Dejar vacío inicialmente
        }
        

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A:AZ')->getAlignment()->setHorizontal('center');

        // Obteniendo el número de columnas dinámicamente
        $columnCount = count($this->headings());
        
        // Definiendo el rango de las celdas que se van a aplicar estilo
        $range = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount) . '1';

        return [
            $range => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '252850', // Color de fondo, puedes cambiarlo según lo necesites
                    ],
                ],
                'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
