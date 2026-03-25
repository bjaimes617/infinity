<?php

    namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class NominaModel extends Model
{
    protected $table = 'dir_nomina';
     protected $fillable = ["id","nombre_nomina","estatus_id"];
    public $timestamps = false;    
}
