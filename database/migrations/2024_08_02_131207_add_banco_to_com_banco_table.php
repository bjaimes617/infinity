<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBancoToComBancoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_banco', function (Blueprint $table) {
            $table->string('banco')->after('lotebanco_id')->nullable();
            $table->string('fecha_pago')->after('banco')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_banco', function (Blueprint $table) {
            $table->dropColumn('banco');
            $table->dropColumn('fecha_pago');
        });
    }
}
