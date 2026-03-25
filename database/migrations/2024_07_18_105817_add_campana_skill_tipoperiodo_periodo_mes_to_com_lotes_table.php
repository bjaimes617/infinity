<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampanaSkillTipoperiodoPeriodoMesToComLotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_lotes', function (Blueprint $table) {
            $table->integer('campana_id')->after('nombre_lote')->unsigned()->index()->nullable();
            $table->foreign('campana_id')->references('id')->on('dir_cartera');

            $table->integer('skill_id')->after('campana_id')->unsigned()->index()->nullable();
            $table->foreign('skill_id')->references('id')->on('dir_skill');

            $table->string('tipo_periodo')->after('skill_id')->nullable();
            $table->string('periodo')->after('tipo_periodo')->nullable();
            $table->string('anio')->after('periodo')->nullable();
            $table->string('mes')->after('anio')->nullable();

        });

        Schema::table('com_operaciones', function (Blueprint $table) {
          
            $table->string('anio')->after('periodo')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_lotes', function (Blueprint $table) {
             
             $table->dropColumn('campana_id');
             $table->dropColumn('skill_id');
             $table->dropColumn('tipo_periodo');
             $table->dropColumn('periodo');
             $table->dropColumn('anio');
             $table->dropColumn('mes');

        });

        Schema::table('com_operaciones', function (Blueprint $table) {
             
             $table->dropColumn('anio');
             
        });
    }
}
