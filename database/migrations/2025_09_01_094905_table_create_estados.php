<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\personal\PersonalModel;
use App\Models\EstadosModel;
use App\Models\RegionModel;
use App\Models\personal\SedeModel;
use App\Models\personal\HistoricoPersonalModel;

class TableCreateEstados extends Migration
{
    /**
     * Run the migrations.
     * MIGRACION NO OPERATIVA SE ELIMINO EL APP.SEDE Y LA TABLA REGIONES
     * @return void
     */
    public function up()
    {
        Schema::create('dir_regiones', function (Blueprint $table) {
            $table->increments('id');                                
            $table->string('nombre_region');                      
            $table->boolean('active');
        });
        
        Schema::create('dir_estados', function (Blueprint $table) {
            $table->increments('id');                                
            $table->string('nombre_estado');            
            $table->integer('region_id')->unsigned()->index();
            $table->foreign('region_id')->references('id')->on('dir_regiones')->cascadeOnDelete();             
            $table->boolean('active');          
        });
              
    $region = ['Capital','Central','Centro-Occidente','Guayana','Andes','Oriente','Zulia'];
        
    foreach($region as $reg){
        $rg = new RegionModel();
        $rg->nombre_region = $reg;
        $rg->active = 1;
        $rg->save();
    }
    
    $estados = [
    ['id' => 1, 'nombre_estado' => 'Miranda', 'region_id' => 1, 'active' => 1],
    ['id' => 2, 'nombre_estado' => 'La Guaira', 'region_id' => 1, 'active' => 1],
    ['id' => 3, 'nombre_estado' => 'Distrito Capital', 'region_id' => 1, 'active' => 1],
    ['id' => 4, 'nombre_estado' => 'Barinas', 'region_id' => 5, 'active' => 1],
    ['id' => 5, 'nombre_estado' => 'Merida', 'region_id' => 5, 'active' => 1],
    ['id' => 6, 'nombre_estado' => 'Tachira', 'region_id' => 5, 'active' => 1],
    ['id' => 7, 'nombre_estado' => 'Trujillo', 'region_id' => 5, 'active' => 1],
    ['id' => 8, 'nombre_estado' => 'Carabobo', 'region_id' => 2, 'active' => 1],
    ['id' => 9, 'nombre_estado' => 'Aragua', 'region_id' => 2, 'active' => 1],
    ['id' => 10, 'nombre_estado' => 'Cojedes', 'region_id' => 2, 'active' => 1],
    ['id' => 11, 'nombre_estado' => 'Apure', 'region_id' => 2, 'active' => 1],
    ['id' => 12, 'nombre_estado' => 'Guarico', 'region_id' => 2, 'active' => 1],
    ['id' => 13, 'nombre_estado' => 'Falcon', 'region_id' => 3, 'active' => 1],
    ['id' => 14, 'nombre_estado' => 'Lara', 'region_id' => 3, 'active' => 1],
    ['id' => 15, 'nombre_estado' => 'Portuguesa', 'region_id' => 3, 'active' => 1],
    ['id' => 16, 'nombre_estado' => 'Yaracuy', 'region_id' => 3, 'active' => 1],
    ['id' => 17, 'nombre_estado' => 'Bolivar', 'region_id' => 4, 'active' => 1],
    ['id' => 18, 'nombre_estado' => 'Amazonas', 'region_id' => 4, 'active' => 1],
    ['id' => 19, 'nombre_estado' => 'Delta Amacuro', 'region_id' => 4, 'active' => 1],
    ['id' => 20, 'nombre_estado' => 'Anzoategui', 'region_id' => 6, 'active' => 1],
    ['id' => 21, 'nombre_estado' => 'Monagas', 'region_id' => 6, 'active' => 1],
    ['id' => 22, 'nombre_estado' => 'Nueva Esparta', 'region_id' => 6, 'active' => 1],
    ['id' => 23, 'nombre_estado' => 'Sucre', 'region_id' => 6, 'active' => 1],
    ['id' => 24, 'nombre_estado' => 'Zulia', 'region_id' => 7, 'active' => 1],
    ];
    
    foreach ($estados as $estado) {
        $est = new EstadosModel();
        $est->nombre_estado = $estado['nombre_estado'];
        $est->region_id     = $estado['region_id'];
        $est->active = 1;
        $est->save();        
    }

        Schema::table('dir_personal', function (Blueprint $table) {
            $table->integer('estado_id')->unsigned()->index()->nullable()->after('hijos');
            $table->foreign('estado_id')->references('id')->on('dir_estados')->nullOnDelete();  
        });
        
         Schema::table('dir_historico_personal', function (Blueprint $table) {
            $table->integer('estado_id')->unsigned()->index()->nullable()->after('hijos');
            $table->foreign('estado_id')->references('id')->on('dir_estados')->nullOnDelete();             
        });
              
        Schema::table('dir_sede', function (Blueprint $table) {
            $table->string('real_name')->after('nombre_sede')->nullable();
            $table->boolean('active')->default(1)->after('real_name');
        });
        
        $config = config('app.sedes');   
        
            foreach(SedeModel::all() as $sedes){ 
            
                if(array_key_exists($sedes->nombre_sede, $config)){
                    $sedes->real_name = $config[$sedes->nombre_sede];
                    $sedes->save();
                }
            }
            
            foreach($config as $key => $newSedes){
                if(!SedeModel::where('nombre_sede',$key)->exists()){
                    $newinset = new SedeModel();
                    $newinset->nombre_sede = $key;
                    $newinset->real_name = $newSedes;
                    $newinset->isp = '0.0.0.0';
                    $newinset->save();
                }
            }
            
                        
            foreach(PersonalModel::all() as $personal){
                               
                $estadoviejo = \App\Models\RegionesModel::find($personal->region_id != null ? $personal->region_id : 5);                
                $idnewEstado = EstadosModel::where('nombre_estado',$estadoviejo->estados)->first()->id;
                
                $personal->estado_id = $idnewEstado;
                if(in_array($personal->sede_laboral, ['STVRO','STVRC','STVRCO'])){
                
                    $personal->sede_laboral = 'STV';                    
                }
                $personal->save();
            }
                        
            foreach(HistoricoPersonalModel::all() as $historico){
                
                $estadoviejo2 = \App\Models\RegionesModel::find($historico->region_id != null ? $personal->region_id : 5);                           
                $idnewEstado2 = EstadosModel::where('nombre_estado',$estadoviejo2->estados)->first()->id;
                
                $historico->estado_id = $idnewEstado2;
                if(in_array($personal->sede_laboral, ['STVRO','STVRC','STVRCO'])){
                
                    $personal->sede_laboral = 'STV';                   
                }
                 $personal->save();
            }
            
        Schema::table('rec_cursos', function (Blueprint $table) {
            $table->integer('estado_id')->unsigned()->index()->nullable()->after('sede');
            $table->foreign('estado_id')->references('id')->on('dir_estados')->nullOnDelete();  
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
            $table->dropForeign('rec_cursos_estado_id_foreign');    
            $table->dropIndex('rec_cursos_estado_id_index');                   
            $table->dropColumn('estado_id');            
        }); 
       
        Schema::table('dir_personal', function (Blueprint $table) {           
            $table->dropForeign('dir_personal_estado_id_foreign');      
            $table->dropIndex('dir_personal_estado_id_index');                 
            $table->dropColumn('estado_id');            
        }); 
        
        Schema::table('dir_historico_personal', function (Blueprint $table) {           
            $table->dropForeign('dir_historico_personal_estado_id_foreign');         
            $table->dropIndex('dir_historico_personal_estado_id_index');              
            $table->dropColumn('estado_id');            
        }); 
        
        Schema::table('dir_sede', function (Blueprint $table) {
            $table->dropColumn('real_name');
            $table->dropColumn('active');
        });
        
        Schema::dropIfExists('dir_estados');
        Schema::dropIfExists('dir_regiones');        
    }
}
