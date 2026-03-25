<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMesAnioPeriodoTipoPeriodoToEvalEvaluacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eval_evaluacion', function (Blueprint $table) {
            $table->string('mes')->nullable()->after('fecha_evaluacion');
            $table->string('anio')->nullable()->after('mes');
            $table->string('tipo_periodo')->nullable()->after('anio');
            $table->string('periodo')->nullable()->after('tipo_periodo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eval_evaluacion', function (Blueprint $table) {
            $table->dropColumn('mes');
            $table->dropColumn('anio');
            $table->dropColumn('tipo_periodo');
            $table->dropColumn('periodo');
        });
    }
}
