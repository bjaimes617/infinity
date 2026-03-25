<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpuestoFechaRemunFechaToAriHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ari_historico', function (Blueprint $table) {
            $table->string('impuesto_fecha')->after('deter_porcentaje')->nullable();
            $table->string('remun_fecha')->after('impuesto_fecha')->nullable();
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
            $table->dropColumn('impuesto_fecha');
            $table->dropColumn('remun_fecha');
        });
    }
}
