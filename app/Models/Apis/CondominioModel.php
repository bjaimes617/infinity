<?php

namespace App\Models\Apis;

use Illuminate\Database\Eloquent\Model;
use App\Models\personal\SedeModel;

class CondominioModel extends Model
{
    protected $table = "dir_condominio";
    
    protected $primaryKey = "id";
    
    public $timestamps = true;
    
    public function relationsede(){
        
        return $this->belongsTo(SedeModel::class,'sede','id');
    }
}
