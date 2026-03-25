<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventarioDirecta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('inv_componentes', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('componente',20); /// DISK/RAM/CPU 
            $table->string('tipo',20)->nullable();///ddr3|ddr2|HDD|SSD
            $table->string('capacidad',10); /// 500/2-4-8/i3
            $table->boolean('estatus_disk',1)->nullable(); /// 500/2-4-8/i3
            $table->string('generacion',10)->nullable(); /// 1ra. 2da. 3ra.
            $table->string('expresion',4); /// MB/ GB / Ghz 
            $table->string('segmento',10); /// proceso / storage 
            $table->string('assigned',1)->nullable();
            $table->string('available',1)->nullable();
            $table->string('bad',1)->nullable();
            $table->timestamps();
        });
        
        Schema::create('inv_equipos', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('tipo');     ///pc,laptop,srv
            $table->string('marca_pc');            
            $table->string('serial_equipo')->nullable();                                      
            $table->string('estado_fuente')->nullable();            
            $table->integer('slot',false);  ///total slots del equipo          
            $table->integer('ram_assigned',false)->nullable();  ///cuantas slot ocupados            
            $table->integer('disks',false); ///cantidad discos instalados                                        
            $table->string('lan_mac_address')->nullable();         
            $table->string('ip')->nullable();
            $table->string('wlan_mac_address')->nullable();  
            $table->string('wip')->nullable();
            $table->date('fecha_mantenimiento')->nullable();            
            $table->string('etiqueta',3)->nullable();
            $table->string('assigned',1)->nullable();
            $table->string('available',1)->nullable();
            $table->string('bad',1)->nullable();     
            $table->timestamps();
        });
        
        Schema::create('inv_monitor', function (Blueprint $table) {
            $table->integerIncrements('id');                      
            $table->string('marca',50)->nullable();
            $table->decimal('pulgadas',5,2);
            $table->string('serial',100)->nullable();            
            $table->string('tipo_alimentador')->nullable(); /// cable / adaptador
            $table->string('etiqueta',3)->nullable();
            $table->string('assigned',1)->nullable();
            $table->string('available',1)->nullable();
            $table->string('bad',1)->nullable();                    
            $table->timestamps();            
        });
        
        Schema::create('inv_posiciones', function (Blueprint $table) {
            $table->integerIncrements('id');  
            $table->integer('sede',false)->nullable();  ///autana/plaza/siemens/maca        
            $table->integer('number',false)->nullable(); ///numero de posicion
            $table->string('area',100)->nullable(); /// si es operaciones, salas, administrativa
            $table->string('coordenada',100)->nullable();            
            $table->string('assigned',1)->nullable();            
            $table->boolean('tipo')->nullable();                          
            $table->unsignedInteger('gerente_id')->index()->foreign('gerente_id')->references('id')->on('dir_personal')->onDelete('null')->nullable();            
            $table->unsignedInteger('cartera_id')->index()->foreign('cartera_id')->references('id')->on('dir_cartera')->onDelete('null')->nullable();
            $table->unsignedInteger('skill_id')->index()->foreign('skill_id')->references('id')->on('dir_skill')->onDelete('null')->nullable();
            $table->string('ceco',5)->nullable();
            $table->string('etiqueta',3)->nullable();
            $table->timestamps();
        });
        
        ///inventario de todos los perifericos
        Schema::create('inv_perifericos', function (Blueprint $table) {
            $table->integerIncrements('id');      
            $table->string('tipo',40);  //teclado,mouse,tarjetainam,headsed
            $table->string('conexion',10);//usb-ps2-pci-3.5mm   
            $table->string('assigned',1)->nullable();
            $table->string('available',1)->nullable();
            $table->string('bad',1)->nullable();   
            $table->timestamps();
        });
        
        Schema::create('inv_equipo_componentes', function (Blueprint $table) { 
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_equipos_id')->index()->foreign('inv_equipos')->references('id')->on('inv_equipos')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_componentes_id')->index()->foreign('inv_componentes')->references('id')->on('inv_componentes')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
        
        Schema::create('inv_equipos_posiciones', function (Blueprint $table) {            
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_equipos_id')->index()->foreign('inv_equipos')->references('id')->on('inv_equipos')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_posiciones_id')->index()->foreign('inv_posiciones_id')->references('id')->on('inv_posiciones')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
        
        Schema::create('inv_equipos_responsables', function (Blueprint $table) {     
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_equipos_id')->index()->foreign('inv_equipos')->references('id')->on('inv_equipos')->onDelete('cascade')->nullable();
            $table->unsignedInteger('personal_id')->index()->foreign('personal_id')->references('id')->on('dir_personal')->onDelete('null')->nullable();
            $table->timestamps();
        });
        
        Schema::create('inv_posiciones_perifericos', function (Blueprint $table) {  
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_perifericos_id')->index()->foreign('inv_perifericos_id')->references('id')->on('inv_perifericos')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_posiciones_id')->index()->foreign('inv_posiciones_id')->references('id')->on('inv_posiciones')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
        
        Schema::create('inv_posiciones_monitor', function (Blueprint $table) {  
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_monitor_id')->index()->foreign('inv_monitor_id')->references('id')->on('inv_monitor')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_posiciones_id')->index()->foreign('inv_posiciones_id')->references('id')->on('inv_posiciones')->onDelete('cascade')->nullable();
            $table->timestamps();
        });        
        
        Schema::create('inv_stocks', function (Blueprint $table) {
            $table->integerIncrements('id');      
            $table->string('tipo',60);  ///sedes
            $table->boolean('active');
        });
        
        Schema::create('inv_stocks_componentes', function (Blueprint $table) {
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_stocks_id')->index()->foreign('inv_stocks_id')->references('id')->on('inv_stocks')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_componentes_id')->index()->foreign('inv_componentes')->references('id')->on('inv_componentes')->onDelete('cascade')->nullable();
        });
        
        Schema::create('inv_stocks_equipos', function (Blueprint $table) {
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_stocks_id')->index()->foreign('inv_stocks_id')->references('id')->on('inv_stocks')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_equipos_id')->index()->foreign('inv_equipos_id')->references('id')->on('inv_equipos')->onDelete('cascade')->nullable();
        });
        
        Schema::create('inv_stocks_perifericos', function (Blueprint $table) {
            $table->integerIncrements('id');      
            $table->unsignedInteger('inv_stocks_id')->index()->foreign('inv_stocks_id')->references('id')->on('inv_stocks')->onDelete('cascade')->nullable();
            $table->unsignedInteger('inv_perifericos_id')->index()->foreign('inv_perifericos_id')->references('id')->on('inv_perifericos')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_posiciones_monitor');
        Schema::dropIfExists('inv_posiciones_perifericos');
        Schema::dropIfExists('inv_equipos_responsables');
        Schema::dropIfExists('inv_equipos_posiciones');
        Schema::dropIfExists('inv_equipo_componentes');
        Schema::dropIfExists('inv_stocks_componentes');
        Schema::dropIfExists('inv_stocks_perifericos');        
        Schema::dropIfExists('inv_stocks_equipos');
        
        Schema::dropIfExists('inv_stocks');
        Schema::dropIfExists('inv_perifericos');
        Schema::dropIfExists('inv_posiciones');
        Schema::dropIfExists('inv_monitor');
        Schema::dropIfExists('inv_componentes');
        Schema::dropIfExists('inv_equipos');
        Schema::dropIfExists('inv_stocks');
    }
}


