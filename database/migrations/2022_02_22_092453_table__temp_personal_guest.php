<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableTempPersonalGuest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('dir_visitantes', function (Blueprint $table) {        
            $table->bigIncrements('id');
            $table->string('cedula_empleado');
            $table->string('primer_nombre');
            $table->string('primer_apellido');            
            $table->string('gerencia');
            
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
        //
    }
}
