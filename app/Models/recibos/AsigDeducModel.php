<?php

namespace App\Models\recibos;

use Illuminate\Database\Eloquent\Model;

class AsigDeducModel extends Model
{
     protected $table = 'rp_asignaciones_deducciones';
     
     public function RelationItems() {      
        return $this->belongsTo(ItemsModels::class,'rp_items_id');    
    }
}
