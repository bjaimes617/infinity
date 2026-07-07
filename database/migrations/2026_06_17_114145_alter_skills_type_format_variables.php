<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\personal\SedeModel;

class AlterSkillsTypeFormatVariables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dir_skill', function (Blueprint $table) {
            $table->json('type_format_uploads')->default('["A"]')->after('tipo_periodo')->nullable();
        });

        $sede = new SedeModel();
        $sede->nombre_sede = "UBIIPAGOS";
        $sede->real_name = "Sede UBii Pagos";
        $sede->active = 1;
        $sede->ISP = '0.0.0.0';
        $sede->save();

        Schema::table('dir_personal', function (Blueprint $table) {
            $table->text('observaciones_egreso')->after('motivo_egreso')->nullable();
        });

        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->text('observaciones_egreso')->after('motivo_egreso')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('dir_skill', function (Blueprint $table) {
            $table->dropColumn('type_format_uploads');
        });

        SedeModel::where('nombre_sede', 'UBIIPAGOS')->delete();

        Schema::table('dir_personal', function (Blueprint $table) {
            $table->dropColumn('observaciones_egreso');
        });

        Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->dropColumn('observaciones_egreso');
        });
    }
}
