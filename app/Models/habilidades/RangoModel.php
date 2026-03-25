<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;

class RangoModel extends Model
{
    protected $table = "hab_rango";

    protected $primaryKey = "id";

    public $timestamps = true;

    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class, 'evaluacion_id','id');
    }


}
