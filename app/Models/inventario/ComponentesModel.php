<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class ComponentesModel extends Model
{
    protected $table = "inv_componentes";
    protected $primaryKey = "id";
    public $timestamps = true;
    
    public function  GetGeneracionAttribute($value){
        $var = '';      
        switch ($value) {
            case 1: $var = "1ra.";
                break;
            case 2:$var = "2da.";
                break;
            case 3:$var = "3ra.";
                break;
            case 4:$var = "4ta.";
                break;
            case 5:$var = "5ta.";
                break;
            case 6:$var = "6ta.";
                break;
            case 7:$var = "7ma.";
                break;
            case 8:$var = "8ta.";
                break;
            case 9:$var = "9na.";
                break;
            case 10:$var = "10ma.";
                break;
            case 11:$var = "11va.";
                break;
            default:
                $var = null;
                break;
        }
        return $var;
    }

    /// para poder borrar los pivots sin borrar el componente
    public function RelationPivotComponenteStocks() {
        return $this->hasOne(RelationComponentesStockModel::class,'inv_componentes_id');
    }
    
     /// para poder borrar los pivots sin borrar el componente
    public function RelationPivotComponenteEquipo() {
        return $this->hasOne(RelationEquipoComponentesModel::class,'inv_componentes_id');
    }
    
    /// relacion hacia el stock donde esta relacionado, devolvera el almacen
    public function RelationComponenteStocks() {
        return $this->belongsToMany(StockModel::class,'inv_stocks_componentes','inv_componentes_id','inv_stocks_id');
    }
    
    public function RelationComponenteEquipo() {
        return $this->belongsToMany(EquipoModel::class,'inv_equipo_componentes','inv_componentes_id','inv_equipos_id');
    }
}
