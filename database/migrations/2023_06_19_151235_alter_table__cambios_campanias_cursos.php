<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCambiosCampaniasCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rec_cparticipantes', function (Blueprint $table) {            
           $table->string('cambiocampana',2)->after('rec_cursos_id')->nullable()->default('NO');
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
           $table->dropColumn('cambiocampana',2);
        });
    }
}
