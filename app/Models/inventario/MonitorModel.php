<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class MonitorModel extends Model
{
    protected $table = "inv_monitor";
    protected $primaryKey = "id";
    public $timestamps = true;
     
    public function getEtiquetaAttribute($value) {
        $var = '';
        switch ($value):
            case 1: $var = "NO IMPRESO";
                break;
            case 2: $var = "IMPRESA";
                break;
            case 3: $var = "DAÑADA";
                break;
        endswitch;
        return $var;
        
    }
    
    public function RelationMonitorPosicion(){
        return $this->belongsToMany(PosicionesModel::class,'inv_posiciones_monitor','inv_monitor_id','inv_posiciones_id');
    }
}
