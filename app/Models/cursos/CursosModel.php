<?php

namespace App\Models\cursos;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\PersonalModel;
use App\Models\EstadosModel;
use App\Models\CiudadesModel;

class CursosModel extends Model
{
     protected $table = 'rec_cursos';
     
    public function getActiveAttribute($value) {
        $var='';
        switch ($value):
            case 0 : $var = "Cerrado";
                break;
            case 1 : $var = "Activo";
                break;
            case 2 : $var = "Inactivo";
                break;           
        endswitch;
        return $var;
    }
    
    
     
    public function RelationCartera() {        
        return $this->belongsTo(CarteraModel::class,'cartera_id');
    }
    public function Relationskill() {        
        return $this->belongsTo(SkillModels::class,'skill_id');
    }
    
    public function RelationPersonal() {        
        return $this->belongsTo(PersonalModel::class,'personal_id');
    }
    
    public function RelationAsistencias() {        
        return $this->hasMany(AsistenciaModel::class,'rec_cursos_id');
    }
    
    public function RelationParticipantes() {        
        return $this->hasMany(ParticipantesModel::class,'rec_cursos_id');
    }
    
    public function RelationEstado() {        
        return $this->belongsTo(EstadosModel::class,'estado_id');
    }
    
    public function RelationCiudad() {        
        return $this->belongsTo(CiudadesModel::class,'ciudad_id');
    }

}
