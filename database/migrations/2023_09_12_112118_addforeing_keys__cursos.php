<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddforeingKeysCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rec_cursos', function (Blueprint $table) {           
            $table->foreign('personal_id')->references('id')->on('dir_personal')->restrictOnUpdate()->onDelete('cascade');            
            $table->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo')->onDelete('cascade');
            $table->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('cascade');
        });
               
        
        Schema::table('rec_observaciones', function (Blueprint $table) {    
            $table->foreign('estatus_id')->references('id')->on('dir_estatus');            
        });
        
        Schema::table('rec_cparticipantes', function (Blueprint $table) {                
            $table->foreign('estatus_id')->references('id')->on('dir_estatus');
            $table->foreign('rec_observacion_id')->references('id')->on('rec_observaciones')->nullable();
            $table->foreign('rec_cursos_id')->references('id')->on('rec_cursos')->onDelete('cascade');            
        });
        
        Schema::table('rec_historial_participante', function (Blueprint $table) {                   
            $table->index('participante_id');
            $table->foreign('participante_id')->references('id')->on('rec_cparticipantes')->onDelete('cascade');                                   
            $table->index('curso_id'); 
            $table->foreign('curso_id')->references('id')->on('rec_cursos')->onDelete('cascade');  
            $table->index('estatus_id'); 
            $table->foreign('estatus_id')->references('id')->on('dir_estatus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
