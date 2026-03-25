<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\inventario\RelationEquiposPosicionesModel;

class PosicionesModel extends Model
{
    protected $table = "inv_posiciones";
    protected $primaryKey = "id";
    public $timestamps = true;
    
    public function ToCartera() {         
         return $this->belongsToMany(CarteraModel::class,'inv_relation_posicion_campanias','inv_posiciones_id','cartera_id');        
    }
    
    public function ToSkill() {         
         return $this->belongsToMany(SkillModels::class,'inv_relation_posicion_campanias','inv_posiciones_id','skill_id');        
    }
    ///relacion para eliminar skills y carteras
    public function RelationSkillsAndCartera() {         
         return $this->hasMany(RelationPosicionCarterasModel::class,'inv_posiciones_id','id');        
    }
    
    ///relacion 1 posicion 1 equipo
    public function PosicionEquipo(){
        return $this->belongsTo(RelationEquiposPosicionesModel::class,'id','inv_posiciones_id');
    }
    
    public function PosicionEquipos(){
        return $this->belongsToMany(EquipoModel::class,'inv_equipos_posiciones','inv_posiciones_id','inv_equipos_id');
    }
        
    ///relacion 1 posicion varios monitores
    public function PosicionMonitores(){
        return $this->belongsToMany(MonitorModel::class,'inv_posiciones_monitor','inv_posiciones_id','inv_monitor_id');        
    }
    ///relacion 1 ponsicion 1 monitor
    public function PosicionMonitor(){
        return $this->belongsTo(RelationPosicionesMonitorModel::class,'id','inv_posiciones_id');
    }
    
    //relacion 1 posicion varios perifericos
    public function PosicionPerifericos(){
        return $this->belongsToMany(PerifericosModel::class,'inv_posiciones_perifericos','inv_posiciones_id','inv_perifericos_id');
    }
    
     ///para eliminar las relaciones
    public function PosicionRelacionPerifericos(){
        return $this->hasMany(RelationPosicionesPerifericosModel::class,'inv_posiciones_id','id');
    }
}
