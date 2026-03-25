<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDirCarteras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_cartera', function (Blueprint $table) {
            $table->string('cod_ceco')->nullable()->after('area_trabajo_id');         
            $table->string('nombre_ceco')->nullable()->after('cod_ceco');         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dir_cartera', function (Blueprint $table) {
            $table->dropColumn('cod_ceco');          
            $table->dropColumn('nombre_ceco');          
        });
    }
}
