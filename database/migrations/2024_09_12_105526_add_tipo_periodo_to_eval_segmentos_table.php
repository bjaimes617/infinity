<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPeriodoToEvalSegmentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eval_segmentos', function (Blueprint $table) {
            $table->string('tipo_periodo')->nullable()->after('cargo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eval_segmentos', function (Blueprint $table) {
            $table->dropColumn('tipo_periodo');
        });
    }
}
