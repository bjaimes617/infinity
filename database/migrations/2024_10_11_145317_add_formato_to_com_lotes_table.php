<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormatoToComLotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('com_lotes', function (Blueprint $table) {
            $table->string('formato')->nullable()->after('lotebanco_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('com_lotes', function (Blueprint $table) {
            $table->dropColumn('formato');
        });
    }
}
