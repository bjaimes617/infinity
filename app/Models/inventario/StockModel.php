<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class StockModel extends Model
{
    protected $table = "inv_stocks";
    protected $primaryKey = "id";
    public $timestamps = true;
          
    /// Componentes por stock
    public function Componentes() {
        return $this->belongsToMany(ComponentesModel::class, 'inv_stocks_componentes', 'inv_stocks_id', 'inv_componentes_id');
    }
    /// Perifericos por stock
    public function Perifericos() {
        return $this->belongsToMany(PerifericosModel::class, 'inv_stocks_perifericos', 'inv_stocks_id', 'inv_perifericos_id');
    }
    /// Equipos por stock
    public function Equipos() {
        return $this->belongsToMany(EquipoModel::class, 'inv_stocks_equipos', 'inv_stocks_id', 'inv_equipos_id');
    }
}
