<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableVisitantes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('dir_visitantes', function (Blueprint $table) {
            $table->boolean('active')->after('gerencia')->default(1);
            $table->bigInteger('user_id')->unsigned()->index()->nullable()->after('active');
            $table->foreign('user_id')->references('id')->on('users');  
            $table->string('cantidad')->after('user_id')->nullable();
            $table->date('expiracion')->nullable()->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dir_visitantes', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->dropIndex('dir_visitantes_user_id_index');
            $table->dropForeign('dir_visitantes_user_id_foreign'); 
            $table->dropColumn('user_id');
            $table->dropColumn('cantidad');            
            $table->dropColumn('expiracion');
        });
    }
}
