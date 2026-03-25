<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\User;

class PermisosCarteraModel extends Model
{
    protected $table = 'dir_permissions_user';    
    public $timestamps = false;
    
    public function RelationAreas() {
        
        return $this->belongsTo(AreatrabajoModel::class,'area_trabajo_id');
    }
    
    public function RelationCarteras() {
        
        return $this->belongsTo(CarteraModel::class,'cartera_id');
    }
    
     public function Relationskill() {
        
        return $this->belongsTo(SkillModels::class,'skill_id');
    }
    
    public function RelationUsers() {
        
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }
}
