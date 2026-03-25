<?php

namespace App\Models\personal;

use Illuminate\Database\Eloquent\Model;

class SkillModels extends Model
{
      protected $table = 'dir_skill';
      
      public function getActiveAttribute($value) {
        $var = '';
        switch ($value):
            case 1 : $var = "Activo";
                break;
            case 2 : $var = "Inactivo";
                break;
        endswitch;
        return $var;
    }

    public function RelationCarteras() {
        return $this->belongsTo(CarteraModel::class,'cartera_id');
    }
}
