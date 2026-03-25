<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estatus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_estatus');
            $table->string('grupo',2);
            $table->integer('visible')->default(1);
            $table->timestamps();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('password_updated_at')->nullable()->after('password');
            $table->unsignedBigInteger('estatus_id')->nullable()->after('password_updated_at');
            $table->foreign('estatus_id')->references('id')->on('estatus')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estatus');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_updated_at');
            $table->dropIndex('users_estatus_id_index');
            $table->dropForeign('users_estatus_id_foreing');
            $table->dropColumn('estatus_id');
        });
    }
}
