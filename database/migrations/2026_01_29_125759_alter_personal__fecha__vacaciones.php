<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPersonalFechaVacaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->date('vacaciones_init')->after('status_carnet')->nullable();
            $table->date('vacaciones_end')->after('vacaciones_init')->nullable();
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->date('vacaciones_init')->after('status_carnet')->nullable();
            $table->date('vacaciones_end')->after('vacaciones_init')->nullable();
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
            $table->dropColumn('vacaciones_init');
            $table->dropColumn('vacaciones_end');
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->dropColumn('vacaciones_init');
            $table->dropColumn('vacaciones_end');
        });
    }
}
