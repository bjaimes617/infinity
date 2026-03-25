<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReclutamientoModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_estatus', function (Blueprint $table) {
            $table->string('group_module')->after('estatus')->default('R')->comment('R de Regular, C de cursos');
        });
        
        Schema::create('rec_observaciones', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('observaciones');
            $table->integer('estatus_id')->index()->foreign('estatus_id')->references('id')->on('dir_estatus')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('rec_cursos', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active');
            $table->boolean('automaticClose')->default(0)->comment('Solo se marcada en 1 si el crontab lo apaga');
            $table->date('inicio');
            $table->date('finaliza');
            $table->integer('requeridos');
            $table->integer('personal_id')->index()->foreign('personal_id')->references('id')->on('dir_personal')->onDelete('cascade');            
            $table->integer('area_trabajo_id')->index()->foreign('area_trabajo_id')->references('id')->on('dir_area_trabajo')->onDelete('cascade');
            $table->integer('cartera_id')->index()->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade');
            $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('cascade');
            $table->string('horario');
            $table->string('sede');            
            $table->string('convodado');
            $table->timestamps();
        });

        Schema::create('rec_cparticipantes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cedula_empleado',50)->unique();
            $table->string('primer_nombre');
            $table->string('segundo_nombre');
            $table->string('primer_apellido');
            $table->string('segundo_apellido');       
            $table->string('especialidad');      
            $table->string('telefono_casa');      
            $table->string('telefono_movil');     
            $table->string('horario_operativo');           
            $table->integer('estatus_id')->index()->foreign('estatus_id')->references('id')->on('dir_estatus')->onDelete('cascade');
            $table->integer('rec_observacion_id')->index()->foreign('rec_observacion_id')->references('id')->on('rec_observaciones')->nullable();
            $table->integer('rec_cursos_id')->index()->foreign('rec_cursos_id')->references('id')->on('rec_cursos')->onDelete('cascade');            
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
        Schema::dropIfExists('rec_observaciones');
        Schema::dropIfExists('rec_cparticipantes');
        Schema::dropIfExists('rec_cursos');
        Schema::table('dir_estatus', function (Blueprint $table) {
            $table->dropColumn('group_module');
        });
    }
}

