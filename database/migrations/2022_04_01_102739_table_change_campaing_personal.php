<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableChangeCampaingPersonal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_cambios_campaing', function (Blueprint $table) {        
            $table->bigIncrements('id');
            $table->Integer('personal_id')->unsigned()->index();
            $table->foreign('personal_id')->references('id')->on('dir_personal')->onDelete('cascade');
            $table->Integer('area_trabajo_id')->unsigned()->index();
            $table->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo')->onDelete('cascade');
            $table->Integer('actual_cartera_id')->unsigned()->index();
            $table->foreign('actual_cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');
            $table->Integer('nueva_cartera_id')->unsigned()->index();
            $table->foreign('nueva_cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');
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
       Schema::dropIfExists('dir_cambios_campaing');
    }
}
