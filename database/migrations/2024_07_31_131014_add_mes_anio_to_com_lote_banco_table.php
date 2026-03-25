<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMesAnioToComLoteBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_lote_banco', function (Blueprint $table) {
            $table->string('mes')->after('lote_banco')->nullable();
            $table->string('anio')->after('mes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_lote_banco', function (Blueprint $table) {
            $table->dropColumn('mes');
            $table->dropColumn('anio');
        });
    }
}
