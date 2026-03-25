<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotebancoIdToComLotesTableAndComBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_lotes', function (Blueprint $table) {
            $table->bigInteger('lotebanco_id')->after('estatus')->unsigned()->index()->nullable();
            $table->foreign('lotebanco_id')->references('id')->on('com_lote_banco');
        });

        Schema::table('com_banco', function (Blueprint $table) {
            $table->bigInteger('lotebanco_id')->after('total_pagar')->unsigned()->index()->nullable();
            $table->foreign('lotebanco_id')->references('id')->on('com_lote_banco');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_lotes', function (Blueprint $table) {
            $table->dropColumn('lotebanco_id');
        });

         Schema::table('com_banco', function (Blueprint $table) {
            $table->dropColumn('lotebanco_id');
        });
    }
}
