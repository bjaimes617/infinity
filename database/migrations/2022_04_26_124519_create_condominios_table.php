<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondominiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_condominio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cedula_empleado');
            $table->string('primer_nombre');
            $table->string('primer_apellido');
            $table->string('empresa');
            $table->string('sede')->nullable();
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
        Schema::dropIfExists('dir_condominio');
    }
}
