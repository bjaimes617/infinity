<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use App\Models\recibos\ItemsModels;
use App\Models\recibos\PeriodosModels;
use App\Models\recibos\RecibosNominaModel;
use App\Models\recibos\AsigDeducModel;
/**
 * Description of RecibosImport
 *
 * @author bjaimes
 */
class RecibosImport implements ToCollection, WithHeadingRow,WithGroupedHeadingRow,WithValidation{
     
    use Importable;
    
    private $request;    
    public function __construct($request) {
        $this->request = $request;
        $arrayBegin = explode("/", $request->mes);
        $fecha = $arrayBegin[1] . "-" . $arrayBegin[0] . "-" . "01";
        
        $this->mes      = date('F', strtotime($fecha));
        $this->anio     = date('Y', strtotime($fecha));
    }


    public function collection(Collection $row){
        //Se valida si existe ya una ejecucion de recibos en el corte, nomina y mes en curso        
        try{
            $periodo = $this->PeriodoCreate();            
            foreach($row as $rows){                
                if(!RecibosNominaModel::where('cedula', $rows["cedula"])->where('rp_periodo_id',$periodo)->exists()){                
                    $nomina = $this->NominaCreate($periodo,$rows);                   
                    $asignacion = $this->AsignaODeducion($nomina->id,$rows); 
                    if($asignacion[0]){
                      $nomina->monto_total = $asignacion["monto"] ;
                      $nomina->save();
                    }
                } else {
                    continue;
                }
            }              
            \Session::flash('success', 'Recibos de Pagos Procesados con Exito, Active el periodo.');   
            return true;
        
        } catch (\Exception $e){
            \Session::flash('error', 'Ocurrio un error al procesar el archivo.');   
            return false; 
        }
    }
    
    private function PeriodoCreate(){
        
        $periodo = new PeriodosModels();
        $periodo->corte         = $this->request->corte;
        $periodo->mes           = $this->mes;
        $periodo->anio          = $this->anio;
        $periodo->tiponomina    = $this->request->nomina;
        $periodo->descripcion   = $this->request->descripcion;
        $periodo->active        = 0;
        $periodo->save();
        
        return $periodo->id;        
    }
    
    private function NominaCreate($periodo, $rows){
        //dd($rows);
        $nomina = new RecibosNominaModel();
        $nomina->cedula = $rows["cedula"]; 
        $nomina->sueldo = $rows["sueldo"];
        $nomina->rp_periodo_id = $periodo;
        $nomina->save();
        
        return $nomina;      
    }
    
    private function AsignaODeducion($nomina,$rows) {    
      
        $i=0;
        $montoAsig = 0;
        $montodeduc = 0;
        $montototal = 0;
        
        foreach($rows["cod"] as $codigo) {                        
           
            if($codigo !== "-"){                  
                $item = ItemsModels::where('coditem',$codigo)->first();                
                $asignacion = new AsigDeducModel(); 
                $asignacion->rp_nomina_id   = $nomina;
                $asignacion->rp_items_id    = $item->id;
                $asignacion->monto          = $rows["monto"][$i];
                $asignacion->porcentaje     = null;
                $asignacion->save();              
                
                if($item->getoriginal('tipo') == 1){
                    /// si el item es Asignacion sumaremos todo
                    $montoAsig = $montoAsig + $rows["monto"][$i]; 
                } else {                   
                    /// si el item es deduccion sumamos todos las deducciones
                   $montodeduc = $montodeduc + $rows["monto"][$i];
                }               
                $i++;
            } else {
                $i++;
            }     
        }       
        $montototal = $montoAsig - $montodeduc;               
        
        return [0=>"true","monto"=>$montototal];
        
    }
    
    public function rules(): array {
        return [           
            'cedula_empleado' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {
                    $onFailure('La cedula del empleado es obligatoria.');
                } else {
                    if (!PersonalModel::where('cedula_empleado', '=', trim($value))->exists()) {
                        $onFailure('La cedula ingresada, no se encuentra registrada.');
                    } else if(PersonalModel::where('cedula_empleado', '=', trim($value))->wherein('estatus_id',[1])->exists() ){
                        $onFailure('La Cedula no posee estatus activo.');
                    }
                }
            },
            'sueldo' => function ($attribute, $value, $onFailure) {
                if (trim($value) == "") {                   
                    $onFailure('El sueldo a cancelar es obligatorio');
                }
            },
            'cod' => function ($attribute, $value, $onFailure) {                
                $array = $value;
                //dd(ItemsModels::whereIn('coditem',$value)->exists());
                if (in_array( null, $value, true)) {                   
                    $onFailure('El Cod de Asignacion o Deducción es Obligatorio');
                } else {                    
                    foreach($value as $val){
                        if(!ItemsModels::where('coditem',$val)->exists() && $val != "-"){
                            $onFailure('El Codigo ingresado '.$val.' no Existe.');
                        }  
                    }                    
                }
            }, 
            'monto' => function ($attribute, $value, $onFailure) {
                if (in_array( null, $value, true)) {                   
                    $onFailure('El Monto de la Asignacion o Deducción es Obligatorio');
                }
            }, 
        ];
    }
       
}
