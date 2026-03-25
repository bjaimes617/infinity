<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableDirConstanciasModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('dir_constancias', function (Blueprint $table){
            $table->integerIncrements('id');
            $table->string('serial');
            $table->string('tipo_contrato');
            $table->string('dirigido_a')->nullable();
            $table->Integer('personal_id')->unsigned()->index();              
            $table->foreign('personal_id')->references('id')->on('dir_personal')->onDelete('cascade'); 
            $table->Integer('cargo_id')->unsigned()->index();              
            $table->foreign('cargo_id')->references('id')->on('dir_cargo')->onDelete('cascade'); 
            $table->date('fecha_ingreso');
            $table->date('fecha_egreso')->nullable();
            $table->date('vigencia_fin');
            $table->decimal('sueldo',10,2);
            $table->Integer('gerente_id')->unsigned()->index();              
            $table->foreign('gerente_id')->references('id')->on('dir_personal')->onDelete('cascade');
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
        Schema::dropIfExists('dir_constancias');       
    }
}
