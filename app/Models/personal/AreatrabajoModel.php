<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CargoModel;
use App\Models\personal\CarteraModel;
class AreatrabajoModel extends Model
{
    protected $table = 'dir_area_trabajo';    
    public $timestamps = false;    
    
    
    public function RelationCarterass() {
        
        return $this->hasMany(CarteraModel::class,'area_trabajo_id');
    }
    
}
