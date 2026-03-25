<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPersonalChecktodoTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_personal', function (Blueprint $table) {
            $table->boolean('todoticket_integral')->after('cuenta_bancaria')->default(0);
            $table->boolean('todoticket_alimentacion')->after('todoticket_integral')->default(0);
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->boolean('todoticket_integral')->after('cuenta_bancaria')->default(0);
            $table->boolean('todoticket_alimentacion')->after('todoticket_integral')->default(0);
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
            $table->dropColumn('todoticket_integral');
             $table->dropColumn('todoticket_alimentacion');
        });
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->dropColumn('todoticket_integral');
             $table->dropColumn('todoticket_alimentacion');
        });
    }
}
