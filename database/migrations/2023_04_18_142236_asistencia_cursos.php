<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AsistenciaCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rec_asistencia', function (Blueprint $table) {         
            $table->bigIncrements('id');
            
            $table->Integer('facilitador_id')->unsigned()->index();            
            $table->foreign('facilitador_id')->references('id')->on('dir_personal')->onDelete('cascade'); 
                        
            $table->Integer('rec_cparticipantes_id')->unsigned()->index();            
            $table->foreign('rec_cparticipantes_id')->references('id')->on('rec_cparticipantes')->onDelete('cascade'); 
            
            $table->Integer('rec_cursos_id')->unsigned()->index();              
            $table->foreign('rec_cursos_id')->references('id')->on('rec_cursos')->onDelete('cascade');                      
            $table->boolean('block')->default(1);            
            $table->string('dia');
            $table->string('prefijo');            
            $table->string('observacion');
            $table->timestamps();       
        });
    }
    
    public function down()
    {
         Schema::dropIfExists('rec_asistencia');
    }
}
