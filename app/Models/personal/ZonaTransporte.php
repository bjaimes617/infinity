<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class ZonaTransporte extends Model
{
    protected $table = 'dir_zonas_transporte';
    
    public function getZonaTransporteAttribute($value) {
        return ucwords(strtolower($value));
    } 
}
