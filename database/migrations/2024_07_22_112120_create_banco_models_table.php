<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancoModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_banco', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('personal_id')->unsigned()->index()->nullable();
            $table->foreign('personal_id')->references('id')->on('dir_personal');

            $table->integer('campana_id')->unsigned()->index()->nullable();
            $table->foreign('campana_id')->references('id')->on('dir_cartera');

            $table->integer('skill_id')->unsigned()->index()->nullable();
            $table->foreign('skill_id')->references('id')->on('dir_skill');

            $table->string('tipo_periodo')->nullable();
            $table->string('periodo')->nullable();
            $table->string('anio')->nullable();
            $table->string('mes')->nullable();

            $table->string('cedula');
            $table->text('observaciones');
            $table->string('usd');
            $table->string('bs_tasa');
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
        Schema::dropIfExists('com_banco');
    }
}
