<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCantidadOrganismoToAriHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ari_historico', function (Blueprint $table) {
            $table->string('cantidad_organismo2')->nullable()->after('cantidad_organismo');
            $table->string('cantidad_organismo3')->nullable()->after('cantidad_organismo2');
            $table->string('cantidad_organismo4')->nullable()->after('cantidad_organismo3');
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
            $table->dropColumn('cantidad_organismo2');
            $table->dropColumn('cantidad_organismo3');
            $table->dropColumn('cantidad_organismo4');
        });
    }
}
