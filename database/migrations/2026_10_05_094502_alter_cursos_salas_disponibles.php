<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCursosSalasDisponibles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rec_cursos_salas', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('sala')->nullable();
            $table->integer('sede_id')->index()->unsigned();
            $table->foreign('sede_id')->references('id')->on('dir_sede');
        });


        $salas = ['Sala de Reuniones', 'Sala 1', 'Sala 2'];

        foreach ($salas as $sala) {
            DB::insert('INSERT INTO rec_cursos_salas (sala, sede_id) VALUES (?, ?)', [$sala, 3]);
        }

        Schema::table('rec_cursos', function (Blueprint $table) {
            $table->string('sala_asignada')->nullable()->after('sede');
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
            $table->dropColumn('sala_asignada');
        });

        Schema::dropIfExists('rec_cursos_salas');
    }
}
