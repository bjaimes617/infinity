<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCargoIdSkillIdToEvalEvaluacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eval_evaluacion', function (Blueprint $table) {
            $table->integer('cargo_id')->unsigned()->index()->nullable()->after('user_id');
            $table->foreign('cargo_id')->references('id')->on('dir_cargo');

            $table->integer('skill_id')->unsigned()->index()->nullable()->after('cargo_id');
            $table->foreign('skill_id')->references('id')->on('dir_skill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eval_evaluacion', function (Blueprint $table) {
            $table->dropColumn('cargo_id');
            $table->dropColumn('skill_id');
        });
    }
}
