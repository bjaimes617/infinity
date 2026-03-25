<?php

namespace App\Models\evaluacion;

use Illuminate\Database\Eloquent\Model;
use App\Models\evaluacion\IndicadorModel;
use App\Models\evaluacion\SegmentoModel;
use App\Models\evaluacion\EvaluacionModel;

class RespuestaModel extends Model
{
    protected $table = "eval_respuestas";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationIndicador(){

        return $this->belongsTo(IndicadorModel::class, 'indicador_id','id');
    }

    public function relationSegmento(){

        return $this->belongsTo(SegmentoModel::class, 'segmento_id','id');
    }

    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class, 'evaluacion_id','id');
        
    }

}
