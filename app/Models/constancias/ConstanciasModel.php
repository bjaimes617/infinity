<?php

namespace App\Models\constancias;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;
use App\Models\personal\CargoModel;


class ConstanciasModel extends Model
{
    protected $table = 'dir_constancias';  
    
   public function getTipoContratoAttribute($value) {
   
        $var='';
        switch ($value):
            case 1 : $var = "Estandar";
                break;
            case 2 : $var = "Personalizado";
                break;           
        endswitch;
        return $var;
    }

    public function RelationCargo() {        
        return $this->belongsTo(CargoModel::class,'cargo_id');
    }
    
    public function RelationPersonal() {  
        return $this->belongsTo(PersonalModel::class,'personal_id');
    }
    
}
