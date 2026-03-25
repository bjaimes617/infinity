<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;
use App\Models\habilidades\CategoriaModel;
use App\Models\habilidades\RespuestaModel;


class PreguntaModel extends Model
{
    protected $table = "hab_preguntas";

    protected $fillable = ['id','cod_pregunta','nombre_pregunta','valor_pregunta','distribucion','evaluacion_id','categoria_id','created_at','updated_at'];

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class,'evaluacion_id','id');
    }

    public function relationCategoria(){

        return $this->belongsTo(CategoriaModel::class,'categoria_id','id');
    }

    public function respuestas(){

        return $this->hasMany(RespuestaModel::class,'pregunta_id','id');
    }
}
