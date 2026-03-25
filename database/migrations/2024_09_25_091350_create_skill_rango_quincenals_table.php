<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillRangoQuincenalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_skill_quincenal', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('gerencia_id')->unsigned()->index()->nullable();
            $table->foreign('gerencia_id')->references('id')->on('dir_area_trabajo');

            $table->integer('campana_id')->unsigned()->index()->nullable();
            $table->foreign('campana_id')->references('id')->on('dir_cartera');            

            $table->integer('skill_id')->unsigned()->index()->nullable();
            $table->foreign('skill_id')->references('id')->on('dir_skill');

            $table->integer('desde1')->nullable();
            $table->integer('hasta1')->nullable();

            $table->integer('desde2')->nullable();
            $table->integer('hasta2')->nullable();

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
        Schema::dropIfExists('dir_skill_quincenal');
    }
}
