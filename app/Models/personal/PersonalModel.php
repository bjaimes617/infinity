<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CargoModel;
use App\Models\personal\LibranzasModel;
use App\Models\personal\EstatusModel;
use App\Models\personal\AreatrabajoModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\personal\EgresadosModel;
use App\Models\asistencia\AsistenciaOperativaModel;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;

class PersonalModel extends Model
{
        
    use SoftDeletes;
    
    protected $table = 'dir_personal';   
    protected $dates = ['deleted_at'];
    
    public function scopeExpiringSoon($query, $days = 7) {
        return $query->whereBetween('finalizacion_1', [now(), now()->addDays($days)])
        ->orWhereBetween('finalizacion_2', [now(), now()->addDays($days)]);
    }

    public function getHorarioOperativoAttribute($value) {
       
        $horarios = config('app.horariolaboral');
        return $value !== null ? $horarios[$value] : '';      
        
    }
    
    public function getEstatusCargoAttribute($value) {
        $var='';
        switch ($value):
            case 1 : $var = "Periodo de Prueba";
                break;
            case 2 : $var = "Encargado";
                break;
            case 3 : $var = "Titular";
                break;            
        endswitch;
        return $var;
    }    
    
    public function getStatusCarnetAttribute($value) {
        $var='';
        switch ($value):
            case 1 : $var = "Emitido";
                break;
            case 2 : $var = "Extraviado";
                break;
            case 3 : $var = "No Impreso";            
        endswitch;
        return $var;
    } 
    
    public function getSituacionContractualAttribute($value) {
        $var='';
        switch ($value):
            case 1 : $var = "Contratado";
                break;
            case 2 : $var = "Fijo";
                break;
            case 3 : $var = "Freelance"; 
                break;
            case 4 : $var = "Honorarios";  
                break;
            case 5 : $var = "Finalizado";  
                break;
            case 6 : $var = "Abandono"; 
                 break;
            case 7 : $var = "Pendiente por contrato"; 
                 break;
            case 8 : $var = "Ingreso No Efectivo"; 
                break;
            case 9 : $var = "Pendiente por Documentacion"; 
                break;
        endswitch;
        return $var;
    }  
    
    public function getJornadaAttribute($value) {
        $var='';
        switch ($value):
            case 1 : $var = "8 Horas";
                break;
            case 2 : $var = "6 Horas";                
        endswitch;
        return $var;
    }   
         
    public function getNivelAcademicoAttribute($value) {
        $var='';
        switch ($value):
            case 1 : $var = "BACHILLER";
                break;
            case 2 : $var = "T.S.U";
                break;
            case 3 : $var = "LIC";
                break;
            case 4 : $var = "ING.";
                break;
            case 5: $var = "OTRO";
                break;
            case 6: $var = "TECNICO MEDIO";
                break;
            case 7: $var = "MAGISTER";
                break;
            case 8: $var = "ESTUDIANTE";
        endswitch;
        return $var;
    }   
    
    public function getMotivoEgresoAttribute($value) {
        return strtolower($value);
    } 
    
    public function RelationCargos() {        
        return $this->belongsTo(CargoModel::class,'cargo_id');
    }    
    
    public function RelationEstatus() {        
        return $this->belongsTo(EstatusModel::class,'estatus_id');
    }
    
    public function RelationAreas() {        
        return $this->belongsTo(AreatrabajoModel::class,'area_trabajo_id');
    }
    
    public function RelationCartera() {        
        return $this->belongsTo(CarteraModel::class,'cartera_id');
    }
    public function Relationskill() {        
        return $this->belongsTo(SkillModels::class,'skill_id');
    }
    
    public function RelationLibranzas() {        
        return $this->belongsTo(LibranzasModel::class,'libranza_id');
    }
    
    public function RelationNomina() {        
        return $this->belongsTo(NominaModel::class,'nomina_id');
    }
    
    public function RelationEstado() {        
        return $this->belongsTo(EstadosModel::class,'estado_id');
    }
    
    public function RelationCiudad() {        
        return $this->belongsTo(\App\Models\CiudadesModel::class,'ciudad_id');
    }    
    
    public function RelationCentroCosto() {        
        return $this->hasmany(CentroCostoAsignadoModel::class,'personal_id');
    }   
    
    public function RelationGerente() {        
        return $this->belongsTo($this,'gerente_area_id');
    }
    
    public function RelationprimerLider() {        
        return $this->belongsTo($this,'jefe_inmediato');
    }
    
    public function RelationsegundoLider() {        
        return $this->belongsTo($this,'jefe_inmediato_segundo');
    }    
    
    public function RelationZonaTransporte() {        
        return $this->belongsTo(ZonaTransporte::class,'zona_transporte_id');
    }
    
    public function RelationEgresados(){        
        return $this->hasMany(EgresadosModel::class, 'dir_personal_id');
    }
    
    public function Relationbanco(){        
        return $this->belongsTo(BancosModels::class, 'bancos_id');
    }
    
    //// relaciones para asistencia operativa
    public function RelationPersonalaCargo(){        
        return $this->hasmany($this,'jefe_inmediato');
    }
    
    public function RelationPersonalacargo2(){        
        return $this->hasmany($this,'jefe_inmediato_segundo');
    }
    
    public function RelationPersonalacargoGerente() {        
        return $this->hasmany($this,'gerente_area_id');
    }
    
    public function RelationListAsistenciaJefeInmediato() {        
        return $this->hasmany(AsistenciaOperativaModel::class,'supervisor_id');
    }
    
    public function RelationListAsistenciaJefeInmediatosegundo() {        
        return $this->hasmany(AsistenciaOperativaModel::class,'coordinador_id');
    }
    
    public function RelationListAsistenciaGerente() {  
        return $this->hasmany(AsistenciaOperativaModel::class,'gerente_id');
    }
    
    public function RelationUser() {  
        return $this->belongsTo(User::class,'user_id');
    }
}