<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespuestaModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eval_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('segmento_id')->unsigned()->index()->nullable();
            $table->foreign('segmento_id')->references('id')->on('eval_segmentos');
            $table->integer('indicador_id')->unsigned()->index()->nullable();
            $table->foreign('indicador_id')->references('id')->on('eval_indicadores');
            
            $table->string('meta')->nullable();
            $table->string('puntaje')->nullable();
            $table->integer('personal_id')->unsigned()->index()->nullable();
            $table->foreign('personal_id')->references('id')->on('dir_personal');

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
        Schema::dropIfExists('eval_respuestas');
    }
}
