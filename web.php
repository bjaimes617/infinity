<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['verify' => true]);
Route::get('/'                               ,'LoginController@index')->name('login');
Route::post('/init'                          ,['uses'=>'LoginController@logininit','middleware' => 'throttle:4,2'])->name('loginin');
Route::get('/checkemail/{token?}/{email?}'   ,'LoginController@activeemail')->name('emailactive');
Route::post('/password/email'                ,['uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail' ,'middleware' => 'throttle:2,120'])->name('password.email');
Route::post("/check/password/{email}"        ,['uses' => 'Auth\ResetPasswordController@checkNewPassword'    ])->name('guest.checknewpassword');
Route::get("/password/reset/{email}/{token}/",['uses' => 'Auth\ResetPasswordController@showResetForm'       ])->name('password.reset.token');
Route::post("/password/reset"                ,['uses' => 'Auth\ResetPasswordController@reset'               ])->name('password.request');

Route::group(['middleware' => ['auth','activity']], function() {    
    Route::get('/home', 'HomeController@index')->name('home');
    //Route::get('/home/runing/', 'HomeController@run')->name('homerum');
    
    ///Permission
    Route::get('permissions/'               ,['uses' => 'PermissionsController@index'   ,'middleware' => 'permission:permissions.show'  ])->name('permissions.index');
    Route::get('permissions/create'         ,['uses' => 'PermissionsController@create'  ,'middleware' => 'permission:permissions.create'])->name('permissions.create');
    Route::post('permissions/create'        ,['uses' => 'PermissionsController@store'   ,'middleware' => 'permission:permissions.create'])->name('permissions.store');    
    Route::get('permissions/edit/{id}'      ,['uses' => 'PermissionsController@edit'    ,'middleware' => 'permission:permissions.edit'  ])->name('permissions.edit');
    Route::put('permissions/{id}/update/'   ,['uses' => 'PermissionsController@update'  ,'middleware' => 'permission:permissions.update'])->name('permissions.update');
    Route::delete('permissions/delete/{id}/',['uses' => 'PermissionsController@destroy' ,'middleware' => 'permission:permissions.delete'])->name('permissions.destroy');
    
    ///Roles
    Route::get('roles/'                 ,['uses' => 'RolesController@index'      ,'middleware' => 'permission:roles.show'  ])->name('roles.index');
    Route::get('roles/create'           ,['uses' => 'RolesController@create'     ,'middleware' => 'permission:roles.create'])->name('roles.create');
    Route::post('roles/create'          ,['uses' => 'RolesController@store'      ,'middleware' => 'permission:roles.create'])->name('roles.store');
    Route::get('roles/edit/{id}'        ,['uses' => 'RolesController@edit'       ,'middleware' => 'permission:roles.edit'  ])->name('roles.edit');
    Route::put('roles/update/{id}'      ,['uses' => 'RolesController@update'     ,'middleware' => 'permission:roles.update'])->name('roles.update');    
    Route::delete('roles/remove/{id}/'  ,['uses' => 'RolesController@remove'     ,'middleware' => 'permission:roles.remove'])->name('roles.remove');
    Route::get('roles/deleted/list'     ,['uses' => 'RolesController@indexRemove','middleware' => 'permission:roles.delete'])->name('roles.remove.index');
    Route::post('roles/delete/{id}/'    ,['uses' => 'RolesController@destroy'    ,'middleware' => 'permission:roles.delete'])->name('roles.destroy');
    Route::get('roles/recovery/{id}/'   ,['uses' => 'RolesController@restore'    ,'middleware' => 'permission:roles.restore'])->name('roles.restore');
    
    ///users    
    Route::get('usuarios/'                  ,['uses' => 'UsersController@index'         ,'middleware' => 'permission:view.users'    ])->name('users.index');
    Route::get('usuarios/create'            ,['uses' => 'UsersController@create'        ,'middleware' => 'permission:create.users'  ])->name('users.create');
    Route::post('usuarios/create'           ,['uses' => 'UsersController@store'         ,'middleware' => 'permission:create.users'  ])->name('users.store');
    Route::get('usuarios/edit/{id}'         ,['uses' => 'UsersController@edit'          ,'middleware' => 'permission:edit.users'    ])->name('users.edit');
    Route::put('usuarios/update/{id}'       ,['uses' => 'UsersController@update'        ,'middleware' => 'permission:update.users'  ])->name('users.update');
    Route::delete('usuarios/remove/{id}/'   ,['uses' => 'UsersController@remove'        ,'middleware' => 'permission:remove.users'  ])->name('users.remove');
    Route::get('usuarios/remove'            ,['uses' => 'UsersController@removeIndex'   ,'middleware' => 'permission:remove.users'  ])->name('users.remove.index');
    Route::delete('usuarios/delete/{id}/'   ,['uses' => 'UsersController@destroy'       ,'middleware' => 'permission:delete.users'  ])->name('users.destroy');
    Route::get('usuarios/recovery/{id}/'    ,['uses' => 'UsersController@restore'       ,'middleware' => 'permission:restore.users' ])->name('users.restore');
    
    Route::get('usuarios/profiles/'             ,['uses' => 'UsersController@profile'              ])->name('users.profile');
    
    Route::get('/usuarios/change'               ,['uses' => 'UsersController@changePassword'        ])->name('users.change');
    Route::post("usuarios/checkcurrentpassword" ,['uses' => 'UsersController@checkCurrentPassword'  ])->name('users.checkcurrentpassword');
    Route::post("usuarios/checknewpassword"     ,['uses' => 'UsersController@checkNewPassword'      ])->name('users.checknewpassword');
    Route::post("usuarios/password"             ,['uses' => 'UsersController@updatePassword'        ])->name('users.password.update');   

///Gestion de Personal
    Route::get('personal/listado/'              ,['uses' => 'personal\PersonalController@index'     ,'middleware' => 'permission:personal.index'   ])->name('personal.index');
    Route::post('personal/buscardor/areas'      ,['uses' => 'personal\PersonalController@create'    ,'middleware' => 'permission:personal.index'   ])->name('buscador.listado');

 // VER FICHA DE PERSONAL
    Route::get('personal/view/{id}'              ,['uses' => 'personal\PersonalController@viewPersonal'])->name('personal.view');
    

    
    ////ajustes - GEstructura Gerencial
    Route::get('personal/ajustes/areas/listado/'                ,['uses' => 'personal\AreasController@index'          ,'middleware' => 'permission:personal.ajustes.areas.index'])->name('personal.ajustes.areas.index');      
     Route::put('personal/areas/listados/update/cartera/{id}'   ,['uses' => 'personal\AreasController@UpdateEstructura' ,'middleware' => 'permission:personal.ajustes.areas.index'  ])->name('areas.carteras.update');
    
    Route::get('personal/ajustes/areas/agregar/'                ,['uses' => 'personal\AreasController@create'                ,'middleware' => 'permission:personal.ajustes.areas.create'  ])->name('personal.ajustes.areas.create');
    Route::post('personal/ajustes/areas/guardar/{tipo}'         ,['uses' => 'personal\AreasController@store'                 ,'middleware' => 'permission:personal.ajustes.areas.create'  ])->name('personal.ajustes.areas.store');
    
    ///selectores de estructura
    Route::post('personal/selector/estructura/areas/'           ,['uses' => 'personal\FindController@selectorAreasEstructuras'      ])->name('selector.areas.estructuras');
    Route::post('personal/selector/estructura/carteras/'        ,['uses' => 'personal\FindController@selectorCarterasEstructuras'   ])->name('selector.carteras.estructuras');
    Route::delete('personal/ajustes/delete/estructuras/'        ,['uses' => 'personal\AreasController@DeleteCarteras','middleware' => 'permission:personal.ajustes.areas.index'  ])->name('personal.ajustes.areas.deleted');
    
    /// permisos sobre carteras
    Route::get('personal/ajustes/permisos/listado/'      ,['uses' => 'personal\AreasController@PermisosList'   ,'middleware' => 'permission:personal.ajustes.permisos.list'    ])->name('personal.ajustes.permisos.list');
    Route::get('personal/ajustes/permisos/agregar/'      ,['uses' => 'personal\AreasController@PermisosCreate' ,'middleware' => 'permission:personal.ajustes.permisos.create'  ])->name('personal.ajustes.permisos.create');
    
    Route::post('personal/selector/permisos/areas_change/'              ,['uses' => 'personal\FindController@SelectorAreasPermisos'           ])->name('select.areas.permisos');
    Route::post('personal/selector/permisos/carteras_change/'           ,['uses' => 'personal\FindController@SelectorCarterasPermisos'        ])->name('select.carteras.permisos');
    Route::post('personal/ajustes/permisos/agregar/'     ,['uses' => 'personal\AreasController@PermisosStore'  ,'middleware' => 'permission:personal.ajustes.permisos.create'  ])->name('personal.ajustes.permisos.store');
    Route::get('personal/ajustes/permisos/editar/{id}'   ,['uses' => 'personal\AreasController@PermisosEdit'   ,'middleware' => 'permission:personal.ajustes.permisos.list'    ])->name('personal.ajustes.permisos.edit');
    Route::put('personal/ajustes/permisos/update/{id}'   ,['uses' => 'personal\AreasController@PermisosUpdate' ,'middleware' => 'permission:personal.ajustes.permisos.list'    ])->name('personal.ajustes.permisos.update');    
    Route::delete('personal/ajustes/permisos/delete/{id}',['uses' => 'personal\AreasController@PermisosDestroy','middleware' => 'permission:personal.ajustes.permisos.list'    ])->name('personal.ajustes.permisos.delete');
        
    ///registrar personal
    Route::get('personal/listado/registrar/new'         ,['uses' => 'personal\PersonalController@addPersonal'   ,'middleware' => 'permission:personal.add'           ])->name('personal.add');    
    Route::post('personal/listado/registrar/new/storage',['uses' => 'personal\PersonalController@store'         ,'middleware' => 'permission:personal.add'           ])->name('personal.store');
    Route::get('personal/listado/editar/{id}/'          ,['uses' => 'personal\PersonalController@edit'          ,'middleware' => 'permission:personal.edit'          ])->name('personal.edit');    
    Route::put('personal/listado/update/{id}/'          ,['uses' => 'personal\PersonalController@update'        ,'middleware' => 'permission:personal.edit'          ])->name('personal.update'); 
    Route::get('personal/listado/remove/{id}/'          ,['uses' => 'personal\PersonalController@remove'        ,'middleware' => 'permission:personal.remove'        ])->name('personal.remove');
    Route::get('personal/listado/deleted'               ,['uses' => 'personal\PersonalController@RemoveIndex'   ,'middleware' => 'permission:personal.delete'        ])->name('personal.delete');
    Route::delete('personal/listado/deleted/{id}'       ,['uses' => 'personal\PersonalController@destroy'       ,'middleware' => 'permission:personal.delete'        ])->name('personal.destroy');
    Route::get('personal/listado/deleted/restore/{id}'  ,['uses' => 'personal\PersonalController@restore'       ,'middleware' => 'permission:personal.delete'        ])->name('personal.restore');
    
    ///registro personal - carga masiva
    Route::get('personal/upload/masive'                 ,['uses' => 'personal\PersonalController@IndexUploadMasive' ,'middleware' => 'permission:personal.upload.masive'        ])->name('personal.upload.masive');
    Route::post('personal/upload/masive'                ,['uses' => 'personal\PersonalController@StoreUploadMasive' ,'middleware' => 'permission:personal.upload.masive'        ])->name('personal.masiva.store');
        
    Route::get('personal/control/print/qr'              ,['uses' => 'personal\ControlAccesoController@index'                  ,'middleware' => 'permission:personal.control.qr'])->name('control.qr');    
    Route::post('personal/control/print/qr'             ,['uses' => 'personal\ControlAccesoController@create'                 ,'middleware' => 'permission:personal.control.qr'])->name('control.qr.list');    
    Route::post('personal/control/qr/generate/'         ,['uses' => 'personal\ControlAccesoController@CreatePrint'            ,'middleware' => 'permission:personal.control.qr'])->name('control.qr.generate');  
    
    //// scaner de QR    
    Route::get('scanner/manual/{sede?}/'                ,['uses' => 'personal\ControlAccesoController@scannermanualQR'          ])->name('control.scanner');
    Route::get('scanner/{sede?}/'                       ,['uses' => 'personal\ControlAccesoController@scannerQR'                ])->name('control.scanner.cam');    
    Route::get('scanner/manual/{id?}/{sede?}/{temp?}'   ,['uses' => 'personal\ControlAccesoController@Checkpersonal'            ])->name('control.scanner.check');
    
    Route::get('personal/visitantes/masive/upload'      ,['uses' => 'personal\ControlAccesoController@IndexVisitantes'          ])->name('personal.visitantes.masive');
    Route::post('personal/visitantes/masive/upload'      ,['uses' => 'personal\ControlAccesoController@storeVisitantes'          ])->name('control.visitantes.store');
    
////////Modulo de Asistencia Operativa
    Route::get('asistencia/personal/operativa'                ,['uses' => 'asistencia\AsistenciaController@index'           ,'middleware' => 'permission:operativa.diaria.module'         ])->name('asistencia.index');    
    Route::get('asistencia/personal/operativa/search'         ,['uses' => 'asistencia\AsistenciaController@create'          ,'middleware' => 'permission:operativa.diaria.create'         ])->name('asistencia.search.create');       
    
    Route::post('asistencia/personal/operativa/sup/'          ,['uses' => 'asistencia\AsistenciaController@store'           ,'middleware' => 'permission:operativa.diaria.create'         ])->name('asistencia.store');    
       
    Route::get('asistencia/personal/operativa/administracion'  ,['uses' => 'asistencia\AsistenciaController@administracion' ,'middleware' => 'permission:operativa.diaria.module'         ])->name('asistencia.administracion');    
    Route::post('asistencia/personal/operativa/administracion' ,['uses' => 'asistencia\AsistenciaController@show'           ,'middleware' => 'permission:operativa.diaria.module'         ])->name('asistencia.administracion.show');    
    
    Route::get('asistencia/personal/operativa/{id}/edit'       ,['uses' => 'asistencia\AsistenciaController@edit'           ,'middleware' => 'permission:operativa.diaria.module'         ])->name('asistencia.administracion.edit');    
    Route::PUT('asistencia/personal/operativa/{id}/update'     ,['uses' => 'asistencia\AsistenciaController@update'         ,'middleware' => 'permission:operativa.diaria.module'         ])->name('asistencia.administracion.update');    
    
    Route::get('asistencia/personal/load/reposos'              ,['uses' => 'asistencia\AsistenciaController@indexReposos'   ,'middleware' => 'permission:operativa.diaria.upload'         ])->name('asistencia.reposos.index');    
    Route::post('asistencia/personal/load/reposos'             ,['uses' => 'asistencia\AsistenciaController@createReposos'  ,'middleware' => 'permission:operativa.diaria.upload'         ])->name('asistencia.reposos.create');    
    
////////Modulo de recibos de pago
    Route::get('recibos/ejecucion/periodos'                     ,['uses' => 'recibos\RecibosController@index'                     ])->name('recibos.index');    
    Route::post('recibos/ejecucion/periodos'                    ,['uses' => 'recibos\RecibosController@create'                    ])->name('recibos.create'); 
    
    Route::get('recibos/activacion/periodos'                    ,['uses' => 'recibos\RecibosController@ActiveIndex'               ])->name('recibos.activacion.index');        
    Route::put('recibos/activacion/periodos/{id}/encurso/{sw?}' ,['uses' => 'recibos\RecibosController@ActiveUpdate'              ])->name('recibos.activacion.update'); 
    Route::delete('recibos/activacion/delete/{id}/period'       ,['uses' => 'recibos\RecibosController@delete'                    ])->name('recibos.activacion.delete'); 
    
    Route::get('recibos/items/detalles/asigdeduc'               ,['uses' => 'recibos\ItemsController@index'                       ])->name('recibos.items.index');    
    Route::get('recibos/items/detalles/create'                  ,['uses' => 'recibos\ItemsController@create'                      ])->name('recibos.items.create');    
    Route::post('recibos/items/detalles/check'                  ,['uses' => 'recibos\ItemsController@checkItems'                  ])->name('recibos.items.check');    
    
    Route::get('recibos/creacion/periodo/actual'                ,['uses' => 'recibos\RecibosController@GenerarIndex'              ])->name('recibos.creacion.index');    
    Route::post('recibos/creacion/periodo/actual'               ,['uses' => 'recibos\RecibosController@GenerarCreate'             ])->name('recibos.creacion.create');    
    
    Route::get('recibos/admin/creacion/periodo/actual'          ,['uses' => 'recibos\RecibosController@IndexAdministracion'       ])->name('recibos.administracion.index');    
    Route::post('recibos/admin/creacion/periodo/actual'         ,['uses' => 'recibos\RecibosController@CreateAdministracion'      ])->name('recibos.administracion.create');    
    
    Route::get('recibos/items/edit/{id}'                        ,['uses' => 'recibos\ItemsController@edit'                        ])->name('recibos.items.edit');    
    Route::PUT('recibos/items/updated/{id}'                     ,['uses' => 'recibos\ItemsController@update'                      ])->name('recibos.items.update');    
    
    Route::post('recibos/check/items'                           ,['uses' => 'recibos\ItemsController@store'                       ])->name('recibos.items.store');    
    
    /////Modulo de Cursos
    Route::get('cursos/creacion'                                 ,['uses' => 'cursos\CursosController@create'                    ])->name('cursos.create');    
    Route::post('cursos/creacion'                                ,['uses' => 'cursos\CursosController@store'                     ])->name('cursos.store');    
    
    Route::get('cursos/creacion/ver'                             ,['uses' => 'cursos\CursosController@index'                     ])->name('cursos.index');
    
    // VER PARTICIPANTES DE LOS CURSOS
    Route::get('cursos/participantes/ver'                             ,['uses' => 'cursos\CursosController@indexParticipantes'                     ])->name('participantes.index');

    Route::post('cursos/participantes/ver/cursos'                             ,['uses' => 'cursos\CursosController@indexCursos'                     ])->name('cursos.index.post');

    
    // BUSCAR PARTICIPANTES
    Route::get('participantes/buscador/'              ,['uses' => 'cursos\CursosController@buscarparticipante'])->name('participante.buscador.index');
    Route::post('participantes/buscador/listado'      ,['uses' => 'cursos\CursosController@listadoParticipante'])->name('buscador.listado.participante');   

    Route::get('cursos/creacion/editar/{idcurso}'                ,['uses' => 'cursos\CursosController@edit'                      ])->name('cursos.edit');    
    Route::put('cursos/creacion/update/{idcurso}'                ,['uses' => 'cursos\CursosController@update'                    ])->name('cursos.update');    
    Route::delete('cursos/creacion/deleted/{idcurso}'            ,['uses' => 'cursos\CursosController@destroy'                   ])->name('cursos.delete');    
    Route::post('change/cursos/estatus/'                         ,['uses' => 'cursos\CursosController@changeEstatusCursos'       ])->name('cursos.change.status');    
    //// modulo cursos - asignacion participantes
    Route::get('cursos/asignacion/index'                         ,['uses' => 'cursos\CursosController@asignacionParticipantesIndex'])->name('cursos.asignacion');    
    Route::post('cursos/asignacion/index'                        ,['uses' => 'cursos\CursosController@asignacionParticipantesStore'])->name('cursos.asignacion.store');
    
    //LISTADO DE PRE-APROBACION
    Route::get('cursos/aprobacion/index'                         ,['uses' => 'cursos\AprobacionesController@aprobacionParticipantesIndex'])->name('cursos.aprobacion'); 

    Route::post('cursos/aprobacion/index'                        ,['uses' => 'cursos\AprobacionesController@aprobacionParticipantesStore'])->name('cursos.lp.store');

    Route::post('cursos/aprobacionlp/show/participantes/{curso?}/' ,['uses' => 'cursos\AprobacionesController@showAprobadoslp'                     ])->name('cursos.aprobacionlp.show'); 

    //// modulo cursos - Asistencia
    Route::get('cursos/asistencia/index'                         ,['uses' => 'cursos\AsistenciaController@Index'                    ])->name('cursos.asistencia.index');    
    Route::post('cursos/asistencia/show/{id?}'                   ,['uses' => 'cursos\AsistenciaController@show'                     ])->name('cursos.asistencia.show');    
    Route::post('cursos/asistencia/store'                        ,['uses' => 'cursos\AsistenciaController@store'                    ])->name('cursos.asistencia.store');    
    //// modificacion de asistencias
    Route::get('cursos/asistencia/create/modifity'               ,['uses' => 'cursos\AsistenciaController@create'                    ])->name('cursos.asistencia.create');    
    Route::post('cursos/asistencia/edit/participantes'           ,['uses' => 'cursos\AsistenciaController@SearchAsistenciasCursos'   ])->name('cursos.asistencia.search');        
    Route::get('cursos/asistencia/participantes/{id}'            ,['uses' => 'cursos\AsistenciaController@edit'                      ])->name('cursos.asistencia.edit');    
    Route::PUT('cursos/asistencia/participantes/{id}'            ,['uses' => 'cursos\AsistenciaController@update'                    ])->name('cursos.asistencia.update');    
    //// aprobacion participantes
    Route::get('cursos/aprobacion/participantes'                 ,['uses' => 'cursos\AprobacionesController@index'                    ])->name('cursos.aprobacion.index');    
    Route::post('cursos/aprobacion/show/participantes/{curso?}/' ,['uses' => 'cursos\AprobacionesController@show'                     ])->name('cursos.aprobacion.show');    
    Route::post('cursos/aprobacion/store/participantes'          ,['uses' => 'cursos\AprobacionesController@store'                    ])->name('cursos.aprobacion.store');    
    Route::post('cursos/selects/aprobacion/estatus'              ,['uses' => 'cursos\AprobacionesController@create'                    ])->name('cursos.aprobacion.create');    
    ///reportes cursos
    Route::get('cursos/reportes/participantes'                  ,['uses' => 'cursos\ReportesController@index'                        ])->name('cursos.reportes.index');    
    Route::post('cursos/reportes/participantes'                 ,['uses' => 'cursos\ReportesController@create'                       ])->name('cursos.reportes.create');

    //MOSTAR PARTICIPANTES DE UN CURSO

    Route::get('cursos/participantes/view/{idcurso}'                ,['uses' => 'cursos\CursosController@showParticipantes'                      ])->name('participantes.showing');

    Route::get('cursos/participantes/edit/{idparticipante}'                ,['uses' => 'cursos\CursosController@editParticipante'                      ])->name('participantes.edit');

    Route::PUT('cursos/participantes/update/{id}'                     ,['uses' => 'cursos\CursosController@updateParticipante'                      ])->name('participante.update');

    Route::delete('cursos/participantes/delete/{id}/',['uses' => 'cursos\CursosController@deleteParticipante'])->name('participante.destroy');

    //HISTORIAL DE PARTICIPANTE

    Route::post('cursos/participantes/historial/'                ,['uses' => 'cursos\CursosController@historial'                      ])->name('historial');


    /////selector de carteras para asistencia operativa
    Route::post('Asistencia/selector/areas/'                    ,['uses' => 'asistencia\AsistenciaController@showSelectAreas'            ])->name('asistencia.operativa.select.areas');
    Route::post('Asistencia/selector/carteras/skl'              ,['uses' => 'asistencia\AsistenciaController@showSelectCarteras'         ])->name('asistencia.operativa.select.carteras');
    
    //// reporte de asistencia
    Route::get('asistencia/personal/reportes/asistencia'      ,['uses' => 'asistencia\ReportesController@index'                   ])->name('asistencia.reporte.index'); 
    Route::post('asistencia/personal/reportes/asistencia'     ,['uses' => 'asistencia\ReportesController@create'                  ])->name('asistencia.reporte.create');    
   
    Route::get('asistencia/personal/reportes/indicadores'               ,['uses' => 'asistencia\ReportesController@indicadoresIndex'        ])->name('asistencia.reporte.indicadores'); 
    Route::post('asistencia/personal/reportes/indicadores'              ,['uses' => 'asistencia\ReportesController@indicadoresCreate'       ])->name('asistencia.reporte.indicadores.create'); 
    ///Selector de Areas de trabajo para el listado de personal
    Route::post('personal/selector/areas/'                              ,['uses' => 'personal\FindController@selectorAreas'                 ])->name('selector.areas');
    Route::post('personal/selector/carteras/'                           ,['uses' => 'personal\FindController@SelectorCarteras'              ])->name('selector.carteras');   
    
    /// selector jefes inmediatos add persona - Areas de trabajo
    Route::post('personal/registrar/cargojefe'                      ,['uses' => 'personal\FindController@cargos_create'           ])->name('check.jefes');
    Route::post('personal/registrar/areas_create_addpersonal'       ,['uses' => 'personal\FindController@AreasCreateAddpersonal'  ])->name('check.areas');    
    Route::post('personal/registrar/carteras_create_addpersonal'   ,['uses' => 'personal\FindController@carterasCreateAddpersonal'  ])->name('check.carteras');    
    
    ////reportes
    Route::get('personal/reportes/'               ,['uses' => 'personal\ReporteController@index'            ,'middleware' => 'permission:personal.reporte.listado'  ])->name('personal.reporte.index');
    Route::post('personal/reportes/'              ,['uses' => 'personal\ReporteController@create'           ,'middleware' => 'permission:personal.reporte.listado'  ])->name('personal.reporte.create');
    
    Route::get('personal/reporte/accesscontrol'        ,['uses' => 'personal\ReporteController@indexaccesscontrol'  ,'middleware' => 'permission:personal.reporte.accesscontrol'])->name('personal.reporte.accesscontrol');
    Route::post('personal/reporte/accesscontrol'       ,['uses' => 'personal\ReporteController@exportaccesscontrol' ,'middleware' => 'permission:personal.reporte.accesscontrol'])->name('personal.reporte.accesscontrol.export');
    
    Route::get('personal/reporte/cecos'           ,['uses' => 'personal\ReporteController@cecogenerate'     ,'middleware' => 'permission:reporte.cecos.registrados'  ])->name('personal.reporte.cecos');

    Route::get('personal/reporte/gerencial'           ,['uses' => 'personal\ReporteController@gerencialgenerate'     ,'middleware' => 'permission:reporte.estructura.gerencial'  ])->name('personal.reporte.gerencial');
        
    Route::get('personal/reporte/egresados/historicos'          ,['uses' => 'personal\ReporteController@indexegresados'  ,'middleware' => 'permission:reporte.historico.egresados'])->name('personal.egresados.index');
    Route::post('personal/reporte/egresados/historicos'         ,['uses' => 'personal\ReporteController@createegresados'  ,'middleware' => 'permission:reporte.historico.egresados'])->name('personal.historico.create');
    
///select centros de costos
    Route::post('personal/selector/centros/costos'  ,['uses' => 'personal\FindController@selectorCeco'])->name('selector.centros.costos');
    Route::post('personal/search/exist'             ,['uses' => 'personal\FindController@searhccedula'])->name('search.exist.personal');
    Route::post('personal/bank/check'               ,['uses' => 'personal\FindController@searhcuentas'])->name('search.exist.cuentas');
        
    ///log Activity
    Route::get('logout',['uses' => 'Auth\LoginController@logout'])->name('logout');
});