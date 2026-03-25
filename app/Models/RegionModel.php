<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EstadosModel;

class RegionModel extends Model
{
    protected $table = 'dir_regiones';
     public $timestamps = false;
     
    public function RelationEstados() {        
        return $this->hasMany(EstadosModel::class,'region_id')->orderBy('nombre_estado', 'ASC');
    }
}
