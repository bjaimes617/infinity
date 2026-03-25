<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;

class EgresadosModel extends Model
{
    protected $table = "dir_egresados";   
    public $timestamps = true;
    
    public function personal(){
        
    return $this->belongsTo(PersonalModel::class, 'dir_personal_id');
    
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
    
    public function RelationprimerLider() {
        
        return $this->belongsTo(PersonalModel::class,'jefe_inmediato');
    }
}
