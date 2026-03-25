<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePersonalCreateUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->nullable()->after('motivo_egreso');
            $table->foreign('user_id')->references('id')->on('users');  
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
            $table->dropColumn('user_id');   
        });
    }
}
