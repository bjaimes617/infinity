<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrimestreToAriUnidadTributariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ari_unidad_tributaria', function (Blueprint $table) {
            $table->string('trimestre')->nullable()->after('anio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ari_unidad_tributaria', function (Blueprint $table) {
            $table->dropColumn('trimestre');
        });
    }
}
