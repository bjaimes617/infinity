<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableAsistenciasOperativas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_asistencia_operativa', function (Blueprint $table) {         
            $table->bigIncrements('id');
            
            $table->string('tipo_asistencia')->nullable();
            
            $table->Integer('supervisor_id')->unsigned()->index();            
            $table->foreign('supervisor_id')->references('id')->on('dir_personal')->onDelete('cascade'); 
            
            $table->Integer('coordinador_id')->unsigned()->index();            
            $table->foreign('coordinador_id')->references('id')->on('dir_personal')->onDelete('cascade'); 
            
            $table->Integer('gerente_id')->unsigned()->index();            
            $table->foreign('gerente_id')->references('id')->on('dir_personal')->onDelete('cascade'); 
            
            $table->Integer('personal_id')->unsigned()->index();              
            $table->foreign('personal_id')->references('id')->on('dir_personal')->onDelete('cascade'); 
            
            $table->Integer('area_trabajo_id')->unsigned()->index();             
            $table->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo')->onDelete('cascade');
            
            $table->Integer('cartera_id')->unsigned()->index();        
            $table->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');             
            $table->boolean('block')->default(1);            
            $table->string('dia');
            $table->string('prefijo');            
            $table->string('observacion');
            $table->timestamps();       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dir_asistencia_operativa');
    }
}