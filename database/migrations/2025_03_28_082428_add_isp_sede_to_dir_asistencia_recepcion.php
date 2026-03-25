<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIspSedeToDirAsistenciaRecepcion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_asistencia_recepcion', function (Blueprint $table) {
            $table->string('isp')->nullable()->after('curso_id');
            $table->string('sede')->nullable()->after('isp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dir_asistencia_recepcion', function (Blueprint $table) {
            $table->dropColumn('isp');
            $table->dropColumn('sede');
        });
    }
}
