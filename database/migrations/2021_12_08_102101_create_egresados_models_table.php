<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEgresadosModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_egresados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->text('motivo');
            $table->integer('jefe_inmediato')->unsigned()->index()->nullable();
            $table->foreign('jefe_inmediato')->references('id')->on('dir_personal')->onDelete('cascade');               
            $table->integer('dir_personal_id')->unsigned()->index();
            $table->foreign('dir_personal_id')->references('id')->on('dir_personal')->onDelete('cascade');            
            $table->unsignedInteger('area_trabajo_id')->nullable()->unsigned()->index()->nullable();
            $table->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo')->onDelete('cascade');             
            $table->unsignedInteger('cartera_id')->nullable()->unsigned()->index()->nullable();
            $table->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade'); 
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
        Schema::dropIfExists('dir_egresados');
    }
}
