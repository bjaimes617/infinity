<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;

class EquipoModel extends Model
{
    protected $table = "inv_equipos";
    protected $primaryKey = "id";
    public $timestamps = true;
    
    public function GetTipoAttribute($param) {
        
        switch ($param) {
            case 1: $val = "LAPTOP";
                break;
            case 2: $val = "PC";
                break;
            case 3: $val = "SRV";
                break;
            default:
                $val = '';
                break;
        }
        return $val;
    }
    
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
    
    ///compomentes por equipo
    public function EquiposAComponentes() {
        return $this->belongsToMany(ComponentesModel::class,'inv_equipo_componentes','inv_equipos_id','inv_componentes_id');
    }
    ///posiciones por equipo
    public function RelationtoPosicion() {         
         return $this->belongsToMany(PosicionesModel::class,'inv_equipos_posiciones','inv_equipos_id','inv_posiciones_id');        
    }
    /// responsables por equipo
    public function EquipoAResponsable() {
        return $this->hasOne(RelationEquiposResponsableModel::class,'inv_equipos_id');
    }
    public function EquipoAstock() {
        return $this->hasOne(RelationEquiposStockModel::class,'inv_equipos_id');
    }
}
