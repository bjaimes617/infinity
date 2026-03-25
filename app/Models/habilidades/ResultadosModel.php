<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;
use App\Models\personal\PersonalModel;
use App\Models\habilidades\ResultadosRespuestasModel;

class ResultadosModel extends Model
{
    protected $table = "hab_resultados";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function evaluacion(){

        return $this->belongsTo(EvaluacionModel::class,'evaluacion_id','id');
    }

    public function personal(){

        return $this->belongsTo(PersonalModel::class, 'personal_id','id');
    }

    public function pregunta(){

        return $this->hasMany(ResultadosRespuestasModel::class, 'pregunta_id','id');
    }

    public function respuesta(){

        return $this->hasMany(ResultadosRespuestasModel::class, 'respuesta_id','id');
    }


}
