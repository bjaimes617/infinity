<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EstadosModel;

class CiudadesModel extends Model
{
    protected $table = 'dir_ciudades';
    
    public function RelationEstados() {        
        return $this->belongsTo(EstadosModel::class,'estado_id')->orderBy('nombre_estado', 'ASC');
    }
}
