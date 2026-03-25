<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RegionModel;

class EstadosModel extends Model
{
    protected $table = 'dir_estados';
    public $timestamps = false;
     
    public function RelationRegion() {        
        return $this->belongsTo(RegionModel::class,'region_id');
    }
    
    public function RelationCiudad() {        
        return $this->hasmany(CiudadesModel::class,'estado_id','id');
    }
}

