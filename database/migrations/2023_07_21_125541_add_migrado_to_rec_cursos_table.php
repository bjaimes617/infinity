<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMigradoToRecCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rec_cursos', function (Blueprint $table) {
            $table->string('migrado')->after('automaticClose')->default('NO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rec_cursos', function (Blueprint $table) {
            $table->dropColumn('migrado');
        });
    }
}
