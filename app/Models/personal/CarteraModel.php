<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class CarteraModel extends Model
{
    protected $table = 'dir_cartera';    
    public $timestamps = false;    
    
    
     public function getEstatusidAttribute($value) {
        switch ($value):
            case 1 : $status = "Activo";
                break;
            case 2 : $status = "Inactivo";     
                    break;
            case 3 : $status = "No usar";     
        endswitch;
        return $status;
    }
    
    public function RelationGerencias() {
        return $this->belongsTo(AreatrabajoModel::class,'area_trabajo_id');
    }
    
    public function Relationskill() {
        return $this->hasmany(SkillModels::class,'cartera_id');
    }
}
