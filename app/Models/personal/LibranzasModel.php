<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class LibranzasModel extends Model
{
    protected $table = 'dir_libranza';
    protected $fillable = ["id","cod_libranza","libranzas","estatus_id"];
    public $timestamps = false;   
}
