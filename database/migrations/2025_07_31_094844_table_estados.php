<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableEstados extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('regiones', function (Blueprint $table) {
            $table->increments('id');                                
            $table->string('region');            
            $table->string('estados');              
            $table->boolean('active');
        });
        
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->Integer('region_id')->unsigned()->index()->nullable()->after('hijos');
            $table->foreign('region_id')->references('id')->on('regiones')->nullOnDelete();  
            
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->Integer('region_id')->unsigned()->index()->nullable()->after('hijos');
            $table->foreign('region_id')->references('id')->on('regiones')->nullOnDelete();  
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regiones');
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->dropIndex('region_id_index');
            $table->dropForeign('region_id_foreign');  
            $table->dropColumn('region_id');              
        });
        
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->dropIndex('region_id_index');
            $table->dropForeign('region_id_foreign');  
            $table->dropColumn('region_id');              
        });
    }
}
