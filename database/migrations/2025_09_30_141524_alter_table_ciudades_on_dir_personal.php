<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCiudadesOnDirPersonal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->integer('ciudad_id')->index()->nullable()->after('estado_id')->default();
            $table->foreign('ciudad_id')->references('id')->on('dir_ciudades')->nullOnDelete();  
            
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->integer('ciudad_id')->index()->nullable()->after('estado_id')->default();
            $table->foreign('ciudad_id')->references('id')->on('dir_ciudades')->nullOnDelete();  
        });
        
        Schema::table('rec_cursos', function (Blueprint $table) {
            $table->integer('ciudad_id')->index()->nullable()->after('estado_id')->default();
            $table->foreign('ciudad_id')->references('id')->on('dir_ciudades')->nullOnDelete();  
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->dropForeign('dir_personal_ciudad_id_foreign');
            $table->dropIndex('dir_personal_ciudad_id_index');            
            $table->dropColumn('ciudad_id');
            
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->dropForeign('dir_historico_personal_ciudad_id_foreign');
            $table->dropIndex('dir_historico_personal_ciudad_id_index');            
            $table->dropColumn('ciudad_id');
        });
        
        Schema::table('rec_cursos', function (Blueprint $table) {
            $table->dropForeign('rec_cursos_personal_ciudad_id_foreign');
            $table->dropIndex('rec_cursos_personal_ciudad_id_index');            
            $table->dropColumn('ciudad_id');
        });
        
       
    }
}
