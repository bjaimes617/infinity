<?php

namespace App\Models\recibos;

use Illuminate\Database\Eloquent\Model;
use App\Models\recibos\AsigDeducModel;

class RecibosNominaModel extends Model
{
     protected $table = 'rp_nomina';
     
     public function RelationAsignaDeducs() {      
        return $this->hasMany(AsigDeducModel::class,'rp_nomina_id');    
    }

    public function RelationNominatoPeriodo() {      
        return $this->belongsTo(PeriodosModels::class,'rp_periodo_id');    
    }


}
