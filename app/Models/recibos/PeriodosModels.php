<?php

namespace App\Models\recibos;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\NominaModel;

class PeriodosModels extends Model
{
    protected $table = 'rp_periodo';
     
     
    public function RelationDirNomina() {      
        return $this->belongsTo(NominaModel::class,'tiponomina');    
    }
    
    public function RelationRecibosNominas() {      
        return $this->hasMany(RecibosNominaModel::class,'rp_periodo_id');    
    }
    
     public function RelationTipoRecibos() {      
        return $this->belongsTo(ContNominaModels::class,'rp_cont_nomina','codigo');    
    }
}
