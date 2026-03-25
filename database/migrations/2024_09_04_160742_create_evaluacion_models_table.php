<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eval_evaluacion', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('fecha_evaluacion');
            $table->integer('personal_id')->unsigned()->index()->nullable();
            $table->foreign('personal_id')->references('id')->on('dir_personal');
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eval_evaluacion');
    }
}
