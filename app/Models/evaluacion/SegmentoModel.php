<?php

namespace App\Models\evaluacion;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CargoModel;
use App\Models\personal\AreatrabajoModel;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;

class SegmentoModel extends Model
{
    protected $table = 'eval_segmentos';
    
    
    public function RelationCargos(){
        return $this->belongsTo(CargoModel::class, 'cargo_id','id');
    }
    
    public function RelationArea(){
        return $this->belongsTo(AreatrabajoModel::class, 'area_trabajo_id','id');
    }
    
    public function RelationCartera(){
        return $this->belongsTo(CarteraModel::class, 'cartera_id','id');
    }
    
    public function RelationSkill(){
        return $this->belongsTo(SkillModels::class, 'skill_id','id');
    }
    
    public function Relationindicadores(){
        return $this->hasMany(IndicadorModel::class, 'eval_segmentos_id','id');
    }
}
