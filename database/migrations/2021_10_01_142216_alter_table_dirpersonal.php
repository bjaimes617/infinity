<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDirpersonal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
               
        Schema::create('centros_costos_asignados', function (Blueprint $table) {            
            $table->increments('id');
            $table->unsignedInteger('personal_id')->nullable()->unsigned()->index();
            $table->foreign('personal_id')->references('id')->on('dir_personal')->onDelete('cascade');  
            
            $table->unsignedInteger('cartera_id')->nullable()->unsigned()->index();
            $table->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade'); 
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
        Schema::dropIfExists('centros_costos_asignados');        
    }
}
