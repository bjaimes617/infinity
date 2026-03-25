<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciaRecepcionModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_asistencia_recepcion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cedula');
            $table->string('nombres');
            $table->integer('curso_id')->unsigned()->index()->nullable();
            $table->foreign('curso_id')->references('id')->on('rec_cursos');
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
        Schema::dropIfExists('dir_asistencia_recepcion');
    }
}
