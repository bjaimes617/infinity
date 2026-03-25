<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePersonalFinalizacionContrato extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->date('finalizacion_1')->nullable()->after('contrato_1');          
            $table->date('finalizacion_2')->nullable()->after('contrato_2');          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->dropColumn('finalizacion_1');
            $table->dropColumn('finalizacion_2');
        });
    }
}
