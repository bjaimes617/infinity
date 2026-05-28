<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CampoCrmPortabilidadExpedientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Creacion de columna para guardar numero de expediente
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->string('usuario_crm_portabilidad')->nullable()->default('No asignado')->after('user_id');
        });

        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->string('usuario_crm_portabilidad')->nullable()->default('No asignado')->after('user_id');
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
            $table->dropColumn('usuario_crm_portabilidad');
        });

        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->dropColumn('usuario_crm_portabilidad');
        });
    }
}
