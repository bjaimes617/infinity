<?php

namespace App\Models\cursos;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\CarteraModel;
use App\Models\personal\SkillModels;
use App\Models\personal\EstatusModel;

class ParticipantesModel extends Model
{
    protected $table = 'rec_cparticipantes';  
    
    public function getHorarioOperativoAttribute($value) {
        $var='';
        switch ($value):
            case 1 : $var = "7:00 AM a 1:00 PM";
                break;
            case 2 : $var = "8:00 AM a 2:00 PM";
                break;
            case 3 : $var = "8:00 AM a 5:00 PM";
                break;
            case 4 : $var = "9:00 AM a 3:00 PM";
                break;
            case 5 : $var = "10:00 AM a 4:00 PM";
                break;
            case 6 : $var = "1:00 PM a 7:00 PM";
                break;
            case 7 : $var = "2:00 PM a 8:00 PM";
                break;
            case 8 : $var = "3:00 PM a 9:00 PM";
                break;
            case 9 : $var = "9:00 AM a 6:00 PM";
                break;
            case 10 : $var = "8:00 AM a 4:00 PM";
                break;
            case 11 : $var = "9:00 AM a 5:00 PM";
                break;
            case 12 : $var = "11:00 AM a 7:00 PM";
                break;
            case 13 : $var = "11:00 AM a 9:00 PM";
                break;
            case 14 : $var = "11:00 AM a 8:00 PM";
        endswitch;
        return $var;
    }
    
    public function RelationCurso() {
        return $this->belongsTo(CursosModel::class,'rec_cursos_id');
    }
    
    public function RelationEstatus() {
        return $this->belongsTo(EstatusModel::class,'estatus_id');
    }
    public function relationObservacionesCurso() {
        return $this->belongsTo(ObservacionesModels::class,'rec_observacion_id');
    }
    
}
