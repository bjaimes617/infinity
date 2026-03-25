<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class RelationPosicionesMonitorModel extends Model
{
    protected $table = "inv_posiciones_monitor";    
    public $timestamps = true;
    
    public function Monitor(){
        return $this->belongsTo(MonitorModel::class, 'inv_monitor_id','id');
    }
}
