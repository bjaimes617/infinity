<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferidoToRecCparticipantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rec_cparticipantes', function (Blueprint $table) {
            $table->integer('referido')->after('segundo_apellido')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rec_cparticipantes', function (Blueprint $table) {
            $table->dropColumn('referido');
        });
    }
}
