<?php

namespace App\Models\cursos;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\EstatusModel;
use App\Models\personal\PersonalModel;

class ObservacionesModels extends Model
{
    protected $table = 'rec_observaciones';     
    
    public function RelationParticipantes() {        
        return $this->belongsTo(EstatusModel::class,'estatus_id');
    }
  
}
