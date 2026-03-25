<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class RelationEquiposPosicionesModel extends Model
{
    protected $table = "inv_equipos_posiciones";    
    public $timestamps = true;
    
    public function Posicion() {
        return $this->belongsTo(PosicionesModel::class,'inv_posiciones_id','id');
    }
     public function equipo() {
        return $this->belongsTo(EquipoModel::class,'inv_equipos_id','id');
    }
}
