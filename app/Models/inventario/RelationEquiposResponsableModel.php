<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;

class RelationEquiposResponsableModel extends Model
{
    protected $table = "inv_equipos_responsables";    
    public $timestamps = true;
    
    ///equipo a Personal
    public function Responsable() {
        return $this->belongsTo(PersonalModel::class,'personal_id');
    }
    //Personal a Equipo
    public function Equipo() {
        return $this->belongsTo(EquipoModel::class,'inv_equipos_id');
    }
    
}
