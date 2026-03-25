<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablesEvaluacionPersonal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('eval_segmentos', function (Blueprint $table) {                
                $table->integerIncrements('id');
                $table->unsignedInteger('area_trabajo_id')->nullable()->unsigned()->index();
                $table->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo')->onDelete('cascade');             
                $table->unsignedInteger('cartera_id')->nullable()->unsigned()->index();
                $table->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');             
                $table->unsignedInteger('skill_id')->nullable()->unsigned()->index();
                $table->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('cascade');             
                $table->unsignedInteger('cargo_id')->nullable()->unsigned()->index();
                $table->foreign('cargo_id')->references('id')->on('dir_cargo')->onDelete('cascade');                
                $table->timestamps();
            });
            
            Schema::create('eval_indicadores', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedInteger('eval_segmentos_id')->nullable()->unsigned()->index();
            $table->foreign('eval_segmentos_id')->references('id')->on('eval_segmentos')->onDelete('cascade');      
            $table->string('indicador');
            $table->string('meta',3);            
            $table->boolean('tipo');
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
         Schema::dropIfExists('eval_indicadores');  
         Schema::dropIfExists('eval_segmentos');  
    }
}
