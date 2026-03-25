<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class VisitantesModel extends Model
{
    protected $table = 'dir_visitantes';
    
    public function RelationUser() {  
        return $this->belongsTo(\App\Models\User::class,'user_id');
    }
}
