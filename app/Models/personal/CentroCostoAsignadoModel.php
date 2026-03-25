<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class CentroCostoAsignadoModel extends Model
{
    protected $table = 'centros_costos_asignados';    
    public $timestamps = true; 
    
    
     public function RelationCECO() {
        
        return $this->belongsTo(SkillModels::class,'skill_id');
    }
}
