<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableInventarioMigracionPosicion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('inv_migraciones', function (Blueprint $table) {
                $table->integerIncrements('id');
                $table->string('sede_salida');
                $table->string('sede_destino');                
                $table->json('solicitud');              ///arreglo con lo que se va a mover
                $table->json('inventario');             ///arreglo con lo que se va a mover                
                $table->integer('user_id');             /// quien la genera                         
                $table->boolean('procesado');
                $table->timestamps();
        });
        
        Schema::table('inv_posiciones', function (Blueprint $table) {
            $table->string('migration')->after('coordenada')->default('0')->nullable();
            $table->unsignedInteger('cargo_id')->index()->foreign('cargo_id')->references('id')->on('dir_cargo')->onDelete('null')->nullable()->after('gerente_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_migraciones');
    }
}
