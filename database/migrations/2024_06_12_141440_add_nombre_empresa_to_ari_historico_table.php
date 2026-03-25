<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreEmpresaToAriHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ari_historico', function (Blueprint $table) {
            $table->string('nombre_empresa')->nullable()->after('unidad_tributaria');
            $table->string('nombre_empresa2')->nullable()->after('nombre_empresa');
            $table->string('nombre_empresa3')->nullable()->after('nombre_empresa2');
            $table->string('nombre_empresa4')->nullable()->after('nombre_empresa3');
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
            $table->dropColumn('nombre_empresa');
            $table->dropColumn('nombre_empresa2');
            $table->dropColumn('nombre_empresa3');
            $table->dropColumn('nombre_empresa4');
        });
    }
}
