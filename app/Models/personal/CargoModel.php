<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;

class CargoModel extends Model
{
     protected $table = 'dir_cargo';
    protected $fillable = ["id","nombre_cargo","estatus_id"];
    public $timestamps = false;
    
    public function RelationPersonal() {
        return $this->hasMany(PersonalModel::class,'cargo_id');
        
    }
}
