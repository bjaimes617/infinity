<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;
use App\Models\habilidades\PreguntaModel;

class CategoriaModel extends Model
{
    protected $table = "hab_categorias";

    protected $fillable = ['id','cod_categoria','nombre_categoria','valor_max','distribucion','evaluacion_id','created_at','updated_at'];

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class,'evaluacion_id','id');
    }

    public function preguntas(){

        return $this->hasMany(PreguntaModel::class,'categoria_id','id');
    }

}
