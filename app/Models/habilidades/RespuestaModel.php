<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;
use App\Models\habilidades\CategoriaModel;
use App\Models\habilidades\PreguntaModel;

class RespuestaModel extends Model
{
    protected $table = "hab_respuestas";

    protected $fillable = ['id','cod_respuesta','nombre_respuesta','valor_respuesta','evaluacion_id','categoria_id','pregunta_id','created_at','updated_at'];

    protected $primaryKey = "id";

    public $timestamps = true;


    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class,'evaluacion_id','id');
    }

    public function relationCategoria(){

        return $this->belongsTo(CategoriaModel::class,'categoria_id','id');
    }

    public function relationPregunta(){

        return $this->belongsTo(PreguntaModel::class,'pregunta_id','id');
    }
}
