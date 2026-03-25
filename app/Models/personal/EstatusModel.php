<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;
use App\Models\cursos\ObservacionesModels;

class EstatusModel extends Model
{
    protected $table = 'dir_estatus';
    protected $fillable = ["id","estatus","estatus_id"];    
    public $timestamps = true;
    
    public function relationObservacionesCurso(){
        return $this->hasMany(ObservacionesModels::class,'estatus_id');
    }
}
