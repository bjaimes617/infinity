<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvTableMultipleCampaniasAndSkill extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_relation_posicion_campanias', function (Blueprint $table) {
            $table->integerIncrements('id');
            
            $table->Integer('inv_posiciones_id')->unsigned()->index();            
            $table->foreign('inv_posiciones_id')->references('id')->on('inv_posiciones')->onDelete('cascade');
            
            $table->Integer('area_trabajo_id')->unsigned()->index()->nullable();            
            $table->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo');
            
            $table->Integer('cartera_id')->unsigned()->index()->nullable();            
            $table->foreign('cartera_id')->references('id')->on('dir_cartera');
            
            $table->Integer('skill_id')->unsigned()->index()->nullable();            
            $table->foreign('skill_id')->references('id')->on('dir_skill');                       
         
            $table->string('ceco', 5)->nullable();
            $table->timestamps();
        });
        
         $posi = App\Models\inventario\PosicionesModel::all();
        foreach ($posi as $pos){
            if($pos->cartera_id != null){
                $relacion = new \App\Models\inventario\RelationPosicionCarterasModel();
                $relacion->inv_posiciones_id = $pos->id;
                $relacion->area_trabajo_id = App\Models\personal\CarteraModel::find($pos->cartera_id)->area_trabajo_id;
                $relacion->cartera_id = $pos->cartera_id;
                $relacion->skill_id = $pos->skill_id;
                $relacion->ceco = $pos->ceco;
                $relacion->save();                
            }
        }
        
         Schema::table('inv_posiciones', function (Blueprint $table) {            
            $table->dropIndex('inv_posiciones_cartera_id_index');
            //$table->dropForeign('inv_posiciones_cartera_id_foreign');
            $table->dropColumn('cartera_id');
            
            $table->dropIndex('inv_posiciones_skill_id_index');
            //$table->dropForeign('inv_posiciones_skill_idd_foreign');
            $table->dropColumn('skill_id'); 
            $table->dropColumn('ceco');
        });
                 

         
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_relation_posicion_campanias');
    }
}
