<?php

namespace App\Models\inventario;

use Illuminate\Database\Eloquent\Model;

class MigracionesModel extends Model
{
    protected $table = "inv_migraciones";
    protected $primaryKey = "id";
    public $timestamps = true;
    
    public function GetSedeSalidaAttribute($param) {
        switch ($param) {
            case 2 : $ab = "PLAZA";
                break;
            case 3 : $ab = "SIEMENS";
                break;
            case 4 : $ab = "AUTANA";
                break;
            case 5 : $ab = "MACARACUAY";
                break;
        }
        return $ab;
    }
     public function GetSedeDestinoAttribute($param) {
        switch ($param) {
            case 2 : $ab = "PLAZA";
                break;
            case 3 : $ab = "SIEMENS";
                break;
            case 4 : $ab = "AUTANA";
                break;
            case 5 : $ab = "MACARACUAY";
                break;
        }
        return $ab;
    }
     public function RelationUsers() {      
        return $this->belongsTo(\App\Models\User::class,'user_id');    
    }
}
