<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableConvocadosAsistencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convocados_asistencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('total_convocados',3);
            $table->Integer('cartera_id')->unsigned()->index();
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
        Schema::dropIfExists('convocados_asistencias');
    }
}
