<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\ResultadosModel;
use App\Models\habilidades\RespuestaModel;
use App\Models\habilidades\PreguntaModel;

class ResultadosRespuestasModel extends Model
{
    protected $table = "hab_resultados_respuestas";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function resultado(){

        return $this->belongsTo(ResultadosModel::class, 'resultado_id','id');
    }

    public function respuesta(){

        return $this->belongsTo(RespuestaModel::class, 'respuesta_id','id');
    }

    public function pregunta(){

        return $this->belongsTo(PreguntaModel::class, 'pregunta_id','id');
    }
}
