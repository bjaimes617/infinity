<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DirPersonalAddColumCtaBancaria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->string('cuenta_bancaria')->nullable()->after('nomina_id');                            
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
            $table->dropColumn('cuenta_bancaria');                    
        });
    }
}
