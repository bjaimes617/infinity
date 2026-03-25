<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluacionIdToEvalRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eval_respuestas', function (Blueprint $table) {
            $table->bigInteger('evaluacion_id')->unsigned()->index()->nullable()->after('puntaje');
            $table->foreign('evaluacion_id')->references('id')->on('eval_evaluacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eval_respuestas', function (Blueprint $table) {
            $table->dropColumn('evaluacion_id');
        });
    }
}
