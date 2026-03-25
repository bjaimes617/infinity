<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TablesRecibosdepagosMFDG extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rp_periodo', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->integer('corte');
            $table->string('mes');
            $table->string('anio');
            $table->string('tiponomina');
            $table->boolean('active')->default(0);
            $table->string('descripcion');
            $table->timestamps();
        });
        
        Schema::create('rp_items', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->string('coditem');
            $table->string('descripcion');
            $table->string('tipo')->default('ASIG - DEDUC');
            $table->timestamps();
        });
        
        Schema::create('rp_nomina', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->string('cedula');
            $table->decimal('sueldo',20,2);
            $table->bigInteger('rp_periodo_id')->unsigned()->index();
            $table->foreign('rp_periodo_id')->references('id')->on('rp_periodo')->onDelete('cascade');
            $table->decimal('monto_total',20,2)->nullable();
            $table->timestamps();
        });
        
        Schema::create('rp_asignaciones_deducciones', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->bigInteger('rp_nomina_id')->unsigned()->index();
            $table->foreign('rp_nomina_id')->references('id')->on('rp_nomina')->onDelete('cascade');
            $table->bigInteger('rp_items_id')->unsigned()->index();
            $table->foreign('rp_items_id')->references('id')->on('rp_items')->onDelete('cascade');
            $table->decimal('monto',20,2);
            $table->string('porcentaje')->nullable();
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
        Schema::dropIfExists('rp_asignaciones_deducciones');
        Schema::dropIfExists('rp_nomina');
        Schema::dropIfExists('rp_items');
        Schema::dropIfExists('rp_periodo');
    }
}