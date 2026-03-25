<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIncidenciaDescripcionIncidenciaToComOperacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_operaciones', function (Blueprint $table) {
            $table->integer('incidencia')->nullable()->after('observaciones');
            $table->text('descripcion_incidencia')->nullable()->after('incidencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_operaciones', function (Blueprint $table) {
            $table->dropColumn('incidencia');
            $table->dropColumn('descripcion_incidencia');
        });
    }
}
