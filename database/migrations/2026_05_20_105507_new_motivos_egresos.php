<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewMotivosEgresos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dir_motivos_egresos', function (Blueprint $table) {
            $table->increments('id');  
            $table->string('descripcion');
            $table->boolean('active')->default(1);          
            $table->timestamps();
        });
        $values = ['Abandono','Ingreso No Efectuado','Culminación de Contrato','Culminacion de Contrato Anticipado','Renuncia','Incumplimientos'];
        
        foreach($values as $value){
            $motivo = new \App\Models\personal\MotivosEgresosModel();           
            $motivo->descripcion = $value;
            $motivo->active = 1;
            $motivo->save();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dir_motivos_egresos');
    }
}
