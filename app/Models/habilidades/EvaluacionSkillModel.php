<?php

namespace App\Models\habilidades;

use Illuminate\Database\Eloquent\Model;
use App\Models\habilidades\EvaluacionModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;


class EvaluacionSkillModel extends Model
{
    protected $table = "hab_evaluacion_skill";

    public function relationEvaluacion(){

        return $this->belongsTo(EvaluacionModel::class,'evaluacion_id','id');
    }

    public function relationGerencia(){

        return $this->belongsTo(AreatrabajoModel::class,'gerencia_id','id');
    }

    public function relationCartera(){

        return $this->belongsTo(CarteraModel::class,'campana_id','id');

    }

    public function relationSkill(){

        return $this->belongsTo(SkillModels::class,'skill_id','id');
    }
}
