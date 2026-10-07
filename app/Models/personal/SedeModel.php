<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\cursos\SalasCursosModel;

class SedeModel extends Model
{
    protected $table = 'dir_sede';
    public $timestamps = false;

    public function RelationSalas()
    {
        return $this->hasMany(SalasCursosModel::class, 'sede_id', 'id');
    }
}
