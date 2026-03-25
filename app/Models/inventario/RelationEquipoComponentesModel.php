<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class RelationEquipoComponentesModel extends Model
{
    protected $table = "inv_equipo_componentes";    
    public $timestamps = true;
    
    public function Componente() {
        return $this->belongsTo(ComponentesModel::class,'inv_componentes_id');
    }
    
    public function Equipo() {
        return $this->belongsTo(EquipoModel::class,'inv_equipos_id');
    }
}
