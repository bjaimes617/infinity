<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToEvalRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eval_respuestas', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->nullable()->after('personal_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eval_respuestas', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
