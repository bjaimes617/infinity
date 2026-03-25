<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class PerifericosModel extends Model
{
    protected $table = "inv_perifericos";
    protected $primaryKey = "id";
    public $timestamps = true;
    
    public function GetTipoAttribute($value) {
        $var = '';
        switch ($value){
          case 1: $var = "TECLADO";              
              break;
          case 2:  $var = "MOUSE";           
              break;
          case 3:  $var = "HEATSET";           
              break;
          case 4:  $var = "T. VIDEO";           
              break;
          case 5:  $var = "T. INALAMBRICA";           
              break;
          case 6:  $var = "CORNETAS";           
              break; 
          case 7:  $var = "ADAPTADORES";           
              break; 
        }
        return $var;
    }
    public function GetConexionAttribute($value) {
        $var = '';
        switch ($value){
          case 1: $var = "USB";              
              break;
          case 2:  $var = "PS/2";           
              break;
          case 3:  $var = "JACK3.5mm";           
              break;
          case 4:  $var = "PCIE";           
              break;
          case 5:  $var = "AGP";           
              break; 
          case 5:  $var = "E/S";           
              break; 
        }
        return $var;
    }
    
    public function RelationPerifericoPosicion() {
        
        return $this->belongsTo(RelationPosicionesPerifericosModel::class,'id','inv_perifericos_id');
        
    }
}