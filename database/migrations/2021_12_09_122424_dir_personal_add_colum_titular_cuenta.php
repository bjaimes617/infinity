<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DirPersonalAddColumTitularCuenta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->string('cedula_titular')->nullable()->after('nomina_id');    
            $table->string('nombre_titular')->nullable()->after('cedula_titular');
            $table->bigInteger('bancos_id')->unsigned()->index()->nullable()->after('nombre_titular');
            $table->foreign('bancos_id')->references('id')->on('dir_bancos')->onDelete('cascade');       
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
            $table->dropColumn('cedula_titular');                    
            $table->dropColumn('nombre_titular');                
        });
    }
}
