<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableSkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('dir_skill', function (Blueprint $table) {
        $table->integerIncrements('id');        
        $table->string('nombre_skill')->nullable();           
        $table->integer('cartera_id')->index()->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('cascade')->nullable();
        $table->boolean('active')->default(1);
        $table->string('cod_ceco',5)->nullable();
        $table->string('nombre_ceco',50)->nullable();             
        $table->timestamps();
    });
        
    Schema::table('dir_personal', function (Blueprint $table) {
       $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('cartera_id');         
    });
    
    Schema::table('dir_cartera', function (Blueprint $table) {
       $table->dropColumn('cod_ceco');
       $table->dropColumn('nombre_ceco');        
    });
    
    Schema::table('convocados_asistencias', function (Blueprint $table) {
       $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('cartera_id');         
    });
    
    Schema::table('dir_egresados', function (Blueprint $table) {
       $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('cartera_id');         
    });
    
    Schema::table('dir_asistencia_operativa', function (Blueprint $table) {
       $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('cartera_id');         
    });
    
    Schema::table('centros_costos_asignados', function (Blueprint $table) {
       $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('cartera_id');         
    });
    
    Schema::table('dir_permissions_user', function (Blueprint $table) {
       $table->integer('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('user_id');         
    });
    
    
    Schema::table('dir_cambios_campaing', function (Blueprint $table) {
       $table->integer('actual_skill_id')->index()->foreign('actual_skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('actual_cartera_id');
       $table->integer('nuevo_skill_id')->index()->foreign('nuevo_skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable()->after('nueva_cartera_id');         
    });
        
    }        
     /* Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::dropIfExists('dir_skill');
    
    Schema::table('dir_personal', function (Blueprint $table) {       
       $table->dropColumn('skill_id');        
    });
    
     Schema::table('convocados_asistencias', function (Blueprint $table) {       
       $table->dropColumn('skill_id');        
    });
    }
}
