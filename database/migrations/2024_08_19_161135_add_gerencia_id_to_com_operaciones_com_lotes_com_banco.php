<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGerenciaIdToComOperacionesComLotesComBanco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_operaciones', function (Blueprint $table) {
            $table->integer('gerencia_id')->unsigned()->index()->nullable()->after('personal_id');
            $table->foreign('gerencia_id')->references('id')->on('dir_area_trabajo');
        });

         Schema::table('com_lotes', function (Blueprint $table) {
            $table->integer('gerencia_id')->unsigned()->index()->nullable()->after('nombre_lote');
            $table->foreign('gerencia_id')->references('id')->on('dir_area_trabajo');
        });

          Schema::table('com_banco', function (Blueprint $table) {
            $table->integer('gerencia_id')->unsigned()->index()->nullable()->after('personal_id');
            $table->foreign('gerencia_id')->references('id')->on('dir_area_trabajo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_operaciones', function (Blueprint $table) {
            $table->dropColumn('gerencia_id');
        });

        Schema::table('com_lotes', function (Blueprint $table) {
            $table->dropColumn('gerencia_id');
        });

        Schema::table('com_banco', function (Blueprint $table) {
            $table->dropColumn('gerencia_id');
        });
    }
}
