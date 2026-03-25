<?php

namespace App\Models\recibos;

use Illuminate\Database\Eloquent\Model;

class ItemsModels extends Model
{
    protected $table = 'rp_items';
    
    
    public function getTipoAttribute($val) {
        $var='';
        switch ($val):
            case 1 : $var = "Asignación";
                break;
            case 2 : $var = "Deducción";
                break; 
            case 3: $var = "Otra Deducción";
                break; 
             case 4 : $var = "Auxiliar de De Deducción";
                break; 
        endswitch;
        return $var;        
    }
    
    public function RelationNomina(){
        return $this->belongsTo(\App\Models\personal\NominaModel::class,'nomina_id','id');
    }
}
