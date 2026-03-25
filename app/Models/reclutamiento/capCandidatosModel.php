<?php

namespace App\Models\reclutamiento;

use Illuminate\Database\Eloquent\Model;

class capCandidatosModel extends Model
{
    protected $table = 'cap_cadidatos';     
 
    
    public function RelationCapEstatus(){
        return $this->belongsTo(capEstatusModel::class, 'cap_status_id', 'id');
    }
}
