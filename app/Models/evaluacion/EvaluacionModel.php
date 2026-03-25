<?php

namespace App\Models\evaluacion;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\PersonalModel;
use App\Models\User;
use App\Models\personal\SkillModels;
use App\Models\personal\CargoModel;

class EvaluacionModel extends Model
{
    protected $table = "eval_evaluacion";

    public function relationPersonal(){

        return $this->belongsTo(PersonalModel::class, 'personal_id','id');
    }

    public function relationUser(){

        return $this->belongsTo(User::class, 'user_id','id');

    }

    public function relationSkill(){

        return $this->belongsTo(SkillModels::class,'skill_id','id');

    }

    public function relationCargo(){

        return $this->belongsTo(CargoModel::class, 'cargo_id','id');
    }

}
