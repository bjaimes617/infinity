<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperacionesQuincenalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_operaciones_quincenal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('personal_id')->unsigned()->index();
            $table->foreign('personal_id')->references('id')->on('dir_personal');

            $table->integer('campana_id')->unsigned()->index();
            $table->foreign('campana_id')->references('id')->on('dir_cartera');

            $table->integer('skill_id')->unsigned()->index();
            $table->foreign('skill_id')->references('id')->on('dir_skill');

            $table->string('comision_usd')->nullable();
            $table->string('variable_usd')->nullable();
            $table->string('bonificacion_usd')->nullable();
            $table->string('bono_noc_usd')->nullable();
            $table->string('he_usd')->nullable();
            $table->string('jornada_fin_usd')->nullable();
            $table->string('descuentos_usd')->nullable();

            $table->string('total_usd')->nullable();
            $table->string('cantidad_ventas')->nullable();

            $table->string('periodo');
            $table->string('mes');

            $table->integer('nomina_id')->unsigned()->index();
            $table->foreign('nomina_id')->references('id')->on('dir_nomina');

            $table->string('ceco')->nullable();

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
        Schema::dropIfExists('com_operaciones_quincenal');
    }
}
