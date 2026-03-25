<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDirvisitantes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('dir_visitantes', function (Blueprint $table) {            
            $table->dropForeign('dir_visitantes_user_id_foreign');  
            $table->dropIndex('dir_visitantes_user_id_index');             
            $table->dropColumn('user_id'); 
            $table->dropColumn('cantidad');
            $table->dropColumn('expiracion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
