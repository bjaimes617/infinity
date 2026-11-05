<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DirEstadisticasPersonalActivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_charts_personal', function (Blueprint $table)
        {
            $table->integerIncrements('id');
            $table->integer('mes', false)->index()->unsigned();
            $table->year('anio')->index();
            $table->integer('cartera_id', false)->unsigned()->index();
            $table->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');
            $table->integer('personal_inicial', false)->index()->nullable();
            $table->integer('personal_final', false)->index()->nullable();
            $table->integer('activos', false)->unsigned();
            $table->integer('requerido', false)->unsigned();

            $table->integer('diferencial', false)->assigned();

            $table->decimal('cumplimiento', 2);
            $table->decimal('rotacion', 2)->nullable();
            $table->decimal('indice', 2)->nullable();
            $table->integer('absentismo', false)->nullable();

            $table->integer('total_ingresos', false)->nullable();
            $table->integer('total_egresos', false)->nullable();
            $table->json('motivos_egresos')->nullable();



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
        Schema::dropIfExists('dir_charts_personal');
    }
}
