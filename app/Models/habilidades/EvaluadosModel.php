<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;
use App\Models\personal\CargoModel;

class EvaluadosModel extends Model
{
    protected $table = "hab_evaluados";

    protected $primaryKey = "id";

    public $timestamps = true;
    
    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class,'evaluacion_id','id');
    }

    public function relationCargoEvaluador(){

        return $this->belongsTo(CargoModel::class,'cargo_evaluador_id','id');
    }

    public function relationCargo(){

        return $this->belongsTo(CargoModel::class,'cargo_evaluar_id','id');
    }

}
