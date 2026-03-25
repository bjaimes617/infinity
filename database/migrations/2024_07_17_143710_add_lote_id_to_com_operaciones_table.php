<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoteIdToComOperacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_operaciones', function (Blueprint $table) {
            $table->bigInteger('lote_id')->after('ceco')->unsigned()->index()->nullable();
            $table->foreign('lote_id')->references('id')->on('com_lotes');
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
            $table->dropColumn('lote_id');
        });
    }
}
