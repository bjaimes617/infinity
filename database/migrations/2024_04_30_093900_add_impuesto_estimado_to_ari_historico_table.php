<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpuestoEstimadoToAriHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ari_historico', function (Blueprint $table) {
            $table->string('impuesto_estimado')->after('porcentaje_reten_ini');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ari_historico', function (Blueprint $table) {
            $table->dropColumn('impuesto_estimado');
        });
    }
}
