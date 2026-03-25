<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddNacionalidadTitularDirPersonal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('dir_personal', function (Blueprint $table) {
            $table->string('nacionalidad_cuenta',1)->nullable()->default('V')->after('rif');
            
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
            $table->dropColumn('nacionalidad_cuenta',1);
            
        });
    }
}
