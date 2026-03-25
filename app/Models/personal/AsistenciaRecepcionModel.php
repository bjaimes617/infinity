<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\cursos\CursosModel;

class AsistenciaRecepcionModel extends Model
{
    protected $table = "dir_asistencia_recepcion";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function curso(){

        return $this->belongsTo(CursosModel::class, 'curso_id','id');
    }

}
