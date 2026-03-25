<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPersonalHtransportes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->boolean('activetransporte')->nullable()->after('direccion');          
            $table->string('htransporte')->nullable()->after('activetransporte');   
            $table->string('estatuscargo')->nullable()->after('cargo_id');   
                     
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
            $table->dropColumn('activetransporte');          
            $table->dropColumn('htransporte');             
             $table->dropColumn('estatuscargo');             
        });
    }
}
