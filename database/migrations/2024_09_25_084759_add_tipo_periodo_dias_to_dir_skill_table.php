<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPeriodoDiasToDirSkillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_skill', function (Blueprint $table) {
            $table->string('tipo_periodo')->nullable()->after('nombre_ceco');
            $table->string('dias')->nullable()->after('tipo_periodo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dir_skill', function (Blueprint $table) {
            $table->dropColumn('tipo_periodo');
            $table->dropColumn('dias');
        });
    }
}
