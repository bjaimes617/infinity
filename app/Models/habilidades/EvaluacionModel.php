<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\CategoriaModel;
use App\Models\habilidades\ResultadosModel;

class EvaluacionModel extends Model
{
    protected $table = "hab_evaluacion";

    protected $fillable = ['id','nombre_evaluacion','valor_max','distribucion','desde','hasta','estatus','created_at','updated_at'];

    protected $primaryKey = "id";

    public $timestamps = true;

    public function categorias(){

        return $this->hasMany(CategoriaModel::class);
    }

    public function resultados(){

        return $this->hasMany(ResultadosModel::class,'evaluacion_id','id');
    }

}
