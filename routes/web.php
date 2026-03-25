<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\Auth\LoginController as Logout;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;

use  App\Http\Controllers\PoliceEstructure;

use App\Http\Controllers\personal\PersonalController;
use App\Http\Controllers\personal\FindController;
use App\Http\Controllers\personal\ControlAccesoController;
use App\Http\Controllers\personal\ReporteController;
use App\Http\Controllers\personal\CalendarController;

use App\Http\Controllers\recibos\RecibosController;
use App\Http\Controllers\recibos\ItemsController;

use App\Http\Controllers\cursos\CursosController;
use App\Http\Controllers\cursos\AsistenciaController as CursosAsistencias;
use App\Http\Controllers\cursos\AprobacionesController;
use App\Http\Controllers\cursos\ReportesController as CursosReportes;

use App\Http\Controllers\arc\ArcController;
use App\Http\Controllers\ari\AriController;

use App\Http\Controllers\constancias\ReporteController as Constancias;

use App\Http\Controllers\asistencia\AsistenciaController;
use App\Http\Controllers\asistencia\ReportesController as AsistenciaReportes;

use App\Http\Controllers\inventario\FindsController as InventarioFinds;
use App\Http\Controllers\inventario\ComponentesController;
use App\Http\Controllers\inventario\PerifericosController;
use App\Http\Controllers\inventario\EquiposController;
use App\Http\Controllers\inventario\PosicionesController;
use App\Http\Controllers\inventario\MigracionController;
use App\Http\Controllers\inventario\ReportesController as InventarioReportes;
use App\Http\Controllers\inventario\UploadsController;
use App\Http\Controllers\inventario\EtiquetaController;

use App\Http\Controllers\evaluacion\EvaluacionesController;
use App\Http\Controllers\evaluacion\FindsController as EvaluacionFinds;

use App\Http\Controllers\comisiones\ComisionesController;

use App\Http\Controllers\habilidades\HabilidadController;

use App\Http\Controllers\reclutamiento\ReclutamientoController;
use App\Http\Controllers\reclutamiento\ReportesController as ReclutamientoReportes;
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

Route::get('/',             [LoginController::class,'index'     ])->middleware(['guest'])->name('login');
Route::post('/init',        [LoginController::class,'logininit' ])->middleware(['throttle:4,2'])->name('loginin');

Route::get('/checkemail/{token?}/{email?}',     [LoginController::class,'activeemail'])->name('emailactive');
Route::post('/password/email',                  [ForgotPasswordController::class,'@sendResetLinkEmail'])->middleware(['throttle:2,0.5'])->name('password.email');
Route::get("/password/reset/{email}/{token}/",  [ResetPasswordController::class,'showResetForm'])->name('password.reset.token');
//Chequeo de contraseña para guest
Route::post("/check/password/{email}",          [ResetPasswordController::class,'checkNewPassword'])->name('guest.checknewpassword');
Route::post("/password/reset",                  [ResetPasswordController::class,'reset'])->name('password.request');

Route::middleware(['auth','activity'])->group(function () {
    
    Route::get('/home',          [HomeController::class,'index'])->name('home');   
///Permission
    Route::get('permissions/',                  [PermissionsController::class,'index'    ])->middleware('permission:permissions.show')->name('permissions.index');
    Route::get('permissions/create',            [PermissionsController::class,'create'   ])->middleware('permission:permissions.create')->name('permissions.create');
    Route::post('permissions/create',           [PermissionsController::class,'store'    ])->middleware('permission:permissions.create')->name('permissions.store');
    Route::get('permissions/edit/{id}',         [PermissionsController::class,'edit'     ])->middleware('permission:permissions.edit')->name('permissions.edit');
    Route::put('permissions/{id}/update/',      [PermissionsController::class,'update'   ])->middleware('permission:permissions.update')->name('permissions.update');
    Route::delete('permissions/delete/{id}/',   [PermissionsController::class,'destroy'  ])->middleware('permission:permissions.delete')->name('permissions.destroy');
///Roles
    Route::get('roles/',                    [RolesController::class,'index'      ])->middleware('permission:roles.show')->name('roles.index');
    Route::get('roles/create',              [RolesController::class,'create'     ])->middleware('permission:roles.create')->name('roles.create');
    Route::post('roles/create',             [RolesController::class,'store'      ])->middleware('permission:roles.create')->name('roles.store');
    Route::get('roles/edit/{id}',           [RolesController::class,'edit'       ])->middleware('permission:roles.edit')->name('roles.edit');
    Route::put('roles/update/{id}',         [RolesController::class,'update'     ])->middleware('permission:roles.update')->name('roles.update');
    Route::delete('roles/remove/{id}/',     [RolesController::class,'remove'     ])->middleware('permission:roles.remove')->name('roles.remove');    
    Route::get('roles/deleted/list',        [RolesController::class,'indexRemove'])->middleware('permission:roles.delete')->name('roles.remove.index');
    Route::delete('roles/delete/{id}/',     [RolesController::class,'destroy'    ])->middleware('permission:roles.delete')->name('roles.destroy');
    Route::put('roles/recovery/{id}/',      [RolesController::class,'restore'    ])->middleware('permission:roles.restore')->name('roles.restore');
///users    
    Route::get('usuarios/',                     [UsersController::class,'index'                   ])->middleware('permission:view.users')->name('users.index');
    Route::post('usuarios/search/permissions/', [UsersController::class,'SearchPermissionsUsers'  ])->middleware('permission:view.users')->name('users.permissions.search');        
    Route::get('usuarios/create',               [UsersController::class,'create'                  ])->middleware('permission:create.users')->name('users.create');
    Route::post('usuarios/add/permissions/',    [UsersController::class,'AddPermissionsAditionals'])->middleware('permission:create.users')->name('users.permissions.add');        
    Route::post('usuarios/create',              [UsersController::class,'store'                   ])->middleware('permission:create.users')->name('users.store');
    Route::get('usuarios/edit/{id}',            [UsersController::class,'edit'                    ])->middleware('permission:edit.users')->name('users.edit');
    Route::put('usuarios/update/{id}',          [UsersController::class,'update'                  ])->middleware('permission:update.users')->name('users.update');
    Route::delete('usuarios/remove/{id}/',      [UsersController::class,'remove'                  ])->middleware('permission:remove.users')->name('users.remove');
    Route::get('usuarios/remove',               [UsersController::class,'removeIndex'             ])->middleware('permission:remove.users')->name('users.remove.index');
    Route::delete('usuarios/delete/{id}/',      [UsersController::class,'destroy'                 ])->middleware('permission:delete.users')->name('users.destroy');
    Route::get('usuarios/recovery/{id}/',       [UsersController::class,'restore'                 ])->middleware('permission:restore.users')->name('users.restore');  
    
    Route::post('usuarios/administracion/tokens',               [UsersController::class,'StoreTokens'             ])->middleware('permission:view.users')->name('users.tokens.store');
    Route::post('usuarios/administracion/list/token',           [UsersController::class,'ShowTokens'              ])->middleware('permission:view.users')->name('users.tokens.show');
    Route::delete('usuarios/administracion/tokens/delete/{id}/',[UsersController::class,'DeleteTokens'            ])->middleware('permission:view.users')->name('users.tokens.delete');
    
    Route::get('usuarios/profiles/',            [UsersController::class,'profile'                ])->name('users.profile');
    Route::get('/usuarios/change',              [UsersController::class,'changePassword'         ])->name('users.change');
//Reinicio de contraseña para usuarios autenticados
    Route::post("usuarios/checkcurrentpassword",    [UsersController::class,'checkCurrentPassword'   ])->name('users.checkcurrentpassword');
    Route::post("usuarios/checknewpassword",        [UsersController::class,'checkNewPassword'       ])->name('users.checknewpassword');
    Route::post("usuarios/password",                [UsersController::class,'updatePassword'         ])->name('users.password.update');
        
///-------------------------policia de Estructura-------------------------///
    Route::post('personal/selector/areas/',         [PoliceEstructure::class,'GerenciaChangeVerified'])->withoutMiddleware(['activity'])->name('selector.areas');
    Route::post('personal/selector/carteras/',      [PoliceEstructure::class,'CampaingChangeVerified'])->withoutMiddleware(['activity'])->name('selector.carteras');
           
///-------------------------Estructura Gerencial
    Route::get('estructura/ajustes/areas/listado/',                 [PoliceEstructure::class,'index'                 ])->middleware('permission:estructura.index.gerencias')->name('estructura.areas.index');
    Route::put('estructura/areas/listados/update/cartera/{id}',     [PoliceEstructure::class,'UpdateEstructura'      ])->middleware('permission:estructura.index.gerencias')->name('estructura.carteras.update');

    Route::get('estructura/ajustes/areas/agregar/',                 [PoliceEstructure::class,'create'                ])->middleware('permission:estructura.create.campaing')->name('estructura.areas.create');
    Route::post('estructura/ajustes/areas/guardar/{tipo}',          [PoliceEstructure::class,'store'                 ])->middleware('permission:estructura.create.campaing')->name('estructura.areas.store');
    Route::delete('estructura/delete/estructuras/',                 [PoliceEstructure::class,'DeleteCarteras'        ])->middleware('permission:estructura.create.campaing')->name('estructura.areas.deleted');
    ///selectores de estructura
    Route::post('estructura/selector/estructura/areas/',            [PoliceEstructure::class,'AreasEstructuras'      ])->name('selector.areas.estructuras');
    Route::post('estructura/selector/estructura/carteras/',         [PoliceEstructure::class,'CarterasEstructuras'   ])->name('selector.carteras.estructuras');
    Route::post('estructura/periodo/skill/',                        [PoliceEstructure::class,'RangoPeriodoSkills'    ])->name('periodo.skill');   
    
    //permisos sobre carteras
    Route::get('estructura/autorizacion/usuarios/listado/',         [PoliceEstructure::class,'PermisosList'      ])->middleware('permission:estructura.permisos.users')->name('estructura.autorizacion.usuarios');    
    Route::post('estructura/usuarios/listado/autorizado',           [PoliceEstructure::class,'PermisosShow'      ])->middleware('permission:estructura.permisos.users')->name('estructura.usuarios.autorizados');    
    
    Route::get('estructura/autorizacion/usuarios/agregar/',         [PoliceEstructure::class,'PermisosCreate'    ])->middleware('permission:estructura.permisos.create')->name('estructura.autorizacion.create');
    Route::post('estructura/autorizacion/usuarios/agregar/',        [PoliceEstructure::class,'PermisosStore'     ])->middleware('permission:estructura.permisos.create')->name('estructura.autorizacion.store');

    Route::get('estructura/autorizacion/permisos/editar/{id}',      [PoliceEstructure::class,'PermisosEdit'      ])->middleware('permission:estructura.permisos.create')->name('estructura.autorizacion.edit');
    Route::put('estructura/autorizacion/permisos/editar/{id}',      [PoliceEstructure::class,'PermisosUpdate'    ])->middleware('permission:estructura.permisos.create')->name('estructura.autorizacion.update');
    Route::delete('estructura/autorizacion/delete/{id}',            [PoliceEstructure::class,'PermisosDestroy'   ])->middleware('permission:estructura.permisos.create')->name('estructura.autorizacion.delete');
    ///reportes de estructura
    Route::get('estructura/reportes/cecos',                         [PoliceEstructure::class,'CecosGenerate'     ])->middleware('permission:estructura.cecos.registrados')->name('estructura.reporte.cecos');
    Route::get('estructura/reportes/gerencial',                     [PoliceEstructure::class,'GerencialGenerate' ])->middleware('permission:estructura.index.gerencias')->name('estructura.reporte.gerencial');

///////////////////////--------------------------------MODULO DE PERSONAL -------------------------///
    Route::post('calendar/consult',     [CalendarController::class,'CountsEvents'       ])->withoutMiddleware(['activity'])->name('personal.calendar.count');    
    Route::get('calendar',              [CalendarController::class,'index'              ])->name('personal.calendar.index');    


    Route::post('personal/selector/estados/',           [FindController::class,'selectorEstados'        ])->withoutMiddleware(['activity'])->name('selector.estados'); 
    Route::post('personal/selector/ciudades/',          [FindController::class,'selectorCiudades'       ])->withoutMiddleware(['activity'])->name('selector.ciudades'); 
    
    Route::get('personal/listado/',                     [PersonalController::class,'index'              ])->middleware('permission:personal.index')->name('personal.index');
    Route::post('personal/buscardor/areas',             [PersonalController::class,'create'             ])->middleware('permission:personal.index')->name('buscador.listado');    
////VER FICHA DE PERSONAL
    Route::get('personal/view/{id}',                    [PersonalController::class,'ViewPersonal'       ])->middleware('permission:personal.index')->name('personal.view');
    Route::get('personal/listado/editar/{id}/',         [PersonalController::class,'edit'               ])->middleware('permission:personal.edit')->name('personal.edit');
    Route::put('personal/listado/update/{id}/',         [PersonalController::class,'update'             ])->middleware('permission:personal.edit')->name('personal.update');
    Route::get('personal/listado/remove/{id}/',         [PersonalController::class,'remove'             ])->middleware('permission:personal.remove')->name('personal.remove');
    Route::get('personal/listado/deleted',              [PersonalController::class,'RemoveIndex'        ])->middleware('permission:personal.delete|personal.restore')->name('personal.delete');
    Route::delete('personal/listado/deleted/{id}',      [PersonalController::class,'destroy'            ])->middleware('permission:personal.delete')->name('personal.destroy');
    Route::get('personal/listado/deleted/restore/{id}', [PersonalController::class,'restore'            ])->middleware('permission:personal.restore')->name('personal.restore');
///historico de cambios 
    Route::get('historial/personal/{id}',               [PersonalController::class,'historial'          ])->middleware(['permission:personal.historial.actualizacion'])->name('historial.personal');
    Route::get('historial/{id}',                        [PersonalController::class,'historialView'      ])->middleware(['permission:personal.historial.actualizacion'])->name('historial.view');
////registro de personal
    Route::get('personal/listado/registrar/new',         [PersonalController::class,'CreatePersonal'    ])->middleware('permission:personal.create')->name('personal.create');
    Route::post('personal/listado/registrar/new/storage',[PersonalController::class,'store'             ])->middleware('permission:personal.create')->name('personal.store');
////Importar masivo
    Route::get('personal/importar/masive',               [PersonalController::class,'IndexUploadMasive'  ])->middleware('permission:personal.import.masive')->name('personal.upload.masive');
    Route::post('personal/importar/masive',              [PersonalController::class,'StoreUploadMasive'  ])->middleware('permission:personal.import.masive')->name('personal.masiva.store');    
////Actualizador masivo
    Route::get('personal/actualizar/masive',             [PersonalController::class,'IndexUpdateMasive'  ])->middleware('permission:personal.update.masive')->name('personal.update.masive');
    Route::post('personal/actualizar/masive',            [PersonalController::class,'UpdateMasive'       ])->middleware('permission:personal.update.masive')->name('personal.masive.update');
    Route::get('personal/control/print/qr',              [ControlAccesoController::class,'index'         ])->middleware('permission:personal.control.qr')->name('control.qr');
    Route::post('personal/control/print/qr',             [ControlAccesoController::class,'create'        ])->middleware('permission:personal.control.qr')->name('control.qr.list');    
    Route::post('personal/control/qr/generate/',         [ControlAccesoController::class,'CreatePrint'   ])->middleware('permission:personal.control.qr')->name('control.qr.generate');
////Selectores Varios
    Route::post('personal/selector/centros/costos',     [FindController::class,'SelectorCeco'               ])->withoutMiddleware(['activity'])->name('selector.centros.costos');
    Route::post('personal/search/exist',                [FindController::class,'SearchCedula'               ])->name('search.exist.personal');
    Route::post('personal/bank/check',                  [FindController::class,'SearchCuentas'              ])->name('search.exist.cuentas');    
    Route::post('personal/registrar/cargosjefe',        [FindController::class,'FindCargosLideres'          ])->withoutMiddleware(['activity'])->name('check.jefes');
    Route::post('personal/find/gerencias/create',       [FindController::class,'AreasCreateAddpersonal'     ])->name('check.areas');
    Route::post('personal/find/carteras/create',        [FindController::class,'carterasCreateAddpersonal'  ])->name('check.carteras');
////Scanner de QR    
    Route::get('scanner/manual/{sede?}/',               [ControlAccesoController::class,'scannermanualQR'   ])->middleware('permission:control.recepciones.autana|control.recepciones.plaza|control.recepciones.macaracuay')->name('control.scanner');
    Route::get('scanner/{sede?}/',                      [ControlAccesoController::class,'scannerQR'         ])->middleware('permission:control.recepciones.autana|control.recepciones.plaza|control.recepciones.macaracuay')->name('control.scanner.cam');
    Route::get('scanner/manual/{id?}/{sede?}',          [ControlAccesoController::class,'Checkpersonal'     ])->middleware('permission:control.recepciones.autana|control.recepciones.plaza|control.recepciones.macaracuay')->name('control.scanner.check');
////Carga de Archivo Visitantes
    Route::get('personal/visitantes/masive/upload',     [ControlAccesoController::class,'IndexVisitantes'   ])->middleware('permission:control.visitantes.cargar')->name('personal.visitantes.masive');
    Route::post('personal/visitantes/masive/upload',    [ControlAccesoController::class,'storeVisitantes'   ])->middleware('permission:control.visitantes.cargar')->name('control.visitantes.store');
////reportes
    Route::get('personal/reportes/',                    [ReporteController::class,'index'                   ])->middleware('permission:personal.reporte.listado')->name('personal.reporte.index');
    Route::post('personal/reportes/',                   [ReporteController::class,'create'                  ])->middleware('permission:personal.reporte.listado')->name('personal.reporte.create');
    Route::get('personal/reporte/historial/egresados',  [ReporteController::class,'indexEgresados'          ])->middleware('permission:personal.reporte.historicoegresado')->name('personal.egresados.index');
    Route::post('personal/reporte/historial/egresados', [ReporteController::class,'createEgresados'         ])->middleware('permission:personal.reporte.historicoegresado')->name('personal.historico.create');   
    Route::get('personal/reporte/accesscontrol',        [ReporteController::class,'indexAccessControl'      ])->middleware('permission:control.reportes|control.reporte.participantescursos')->name('personal.reporte.accesscontrol');
    Route::post('personal/reporte/accesscontrol',       [ReporteController::class,'exportAccessControl'     ])->middleware('permission:control.reportes|control.reporte.participantescursos')->name('personal.reporte.accesscontrol.export');
//RUTAS PARA HISTORIAL DEL LP
    Route::get('personal/reporte/actualizaciones/cambios',    [ReporteController::class,'ReporteActualizacion'   ])->middleware(['permission:personal.reporte.actualizacion'])->name('reporte.actualizacion');
    Route::post('personal/reporte/actualizaciones/cambios',   [ReporteController::class,'ReporteActualizaciones' ])->middleware(['permission:personal.reporte.actualizacion'])->name('reporte.actualizacion.view');                  
    
    Route::get('personal/reporte/historicos/registrados/',    [ReporteController::class,'ReporteHistorico'       ])->middleware(['permission:personal.reporte.actualizacion'])->name('reporte.historico');
    Route::post('personal/reporte/historicos/registrados/',   [ReporteController::class,'ReporteHistoricos'      ])->middleware(['permission:personal.reporte.actualizacion'])->name('reporte.historico.view');
    
//RUTAS PARA REPORTE DE REPLICAS
    Route::get('personal/reporte/replicas/expedientes/',    [PersonalController::class, 'IndexReplicasExpedientes'   ])->middleware(['permission:personal.reporte.replicas'])->name('reporte.replicas');
    Route::post('personal/reporte/replicas/expedientes/',   [PersonalController::class, 'ShowReplicasExpedientes'    ])->middleware(['permission:personal.reporte.replicas'])->name('reporte.replicas.view');

    
///////////////////////--------------------------------RECIBOS DE PAGOS -------------------------/// 
    Route::get('recibos/ejecucion/periodos',                        [RecibosController::class,'index'            ])->middleware(['permission:recibos.upload.manual'])->name('recibos.index');
    Route::post('recibos/ejecucion/periodos',                       [RecibosController::class,'create'           ])->middleware(['permission:recibos.upload.manual'])->name('recibos.create');
/// ACTIVAR Y ELIMINAR RECIBOS DE PAGOS
    Route::get('recibos/activacion/periodos',                       [RecibosController::class,'ActiveIndex'      ])->middleware(['permission:recibos.activacion.periodos'])->name('recibos.activacion.index');
    Route::post('recibos/activacion/search/periodos',               [RecibosController::class,'SearchPeriodos'   ])->name('recibos.search.periodos.active');        
    Route::put('recibos/activacion/periodos/{id}/encurso/{sw?}',    [RecibosController::class,'ActiveUpdate'     ])->middleware(['permission:recibos.activacion.periodos'])->name('recibos.activacion.update');    
    Route::delete('recibos/activacion/delete/{id}/period',          [RecibosController::class,'delete'           ])->middleware(['permission:recibos.activacion.periodos'])->name('recibos.activacion.delete');
///ITEMS DE LOS RECIBOS, ASIGNACIONES Y DEDUCCIONES
    Route::get('recibos/items/detalles/asigdeduc',                  [ItemsController::class,'index'              ])->middleware(['permission:recibos.ajustes.items'])->name('recibos.items.index');
    Route::get('recibos/items/detalles/create',                     [ItemsController::class,'create'             ])->middleware(['permission:recibos.ajustes.items'])->name('recibos.items.create');
    Route::post('recibos/items/detalles/check',                     [ItemsController::class,'checkItems'         ])->middleware(['permission:recibos.ajustes.items'])->name('recibos.items.check');    
    Route::get('recibos/items/edit/{id}',                           [ItemsController::class,'edit'               ])->middleware(['permission:recibos.ajustes.items'])->name('recibos.items.edit');
    Route::PUT('recibos/items/updated/{id}',                        [ItemsController::class,'update'             ])->middleware(['permission:recibos.ajustes.items'])->name('recibos.items.update');
    Route::post('recibos/check/items',                              [ItemsController::class,'store'              ])->middleware(['permission:recibos.ajustes.items'])->name('recibos.items.store');
///CREACION DE RECIBOS DE PAGO POR GERENTES
    Route::get('recibos/creacion/periodo/actual',                   [RecibosController::class,'GenerarIndex'     ])->middleware(['permission:recibos.creacion.gerentes'])->name('recibos.creacion.index');
    Route::post('recibos/creacion/periodo/actual',                  [RecibosController::class,'GenerarCreate'    ])->middleware(['permission:recibos.creacion.gerentes'])->name('recibos.creacion.create');
/// RECIBOS DE PAGOS GENERADOS POR CEDULA
    Route::get('recibos/admin/creacion/periodo/actual',             [RecibosController::class,'IndexAdministracion'  ])->middleware(['permission:recibos.administrador|recibos.verificar.cargas'])->name('recibos.administracion.index');
    Route::post('recibos/admin/creacion/periodo/actual',            [RecibosController::class,'CreateAdministracion' ])->middleware(['permission:recibos.administrador|recibos.verificar.cargas'])->name('recibos.administracion.create');

    Route::get('recibos/admin/reporte/nominas',             [RecibosController::class,'IndexReporteAdministracion'  ])->middleware(['permission:recibos.administrador.reportes'])->name('recibos.administracion.reporte.index');
    Route::post('recibos/admin/reporte/nominas',            [RecibosController::class,'CreateReporteAdministracion' ])->middleware(['permission:recibos.administrador.reportes'])->name('recibos.administracion.reporte.create');
    
  ///////////////////////--------------------------------CURSOS -------------------------/// 
    Route::get('cursos/creacion/ver',               [CursosController::class,'index'          ])->middleware(['permission:cursos.module'])->name('cursos.index');  
    Route::post('cursos/search',                    [CursosController::class,'SearchCursos'   ])->middleware(['permission:cursos.module'])->name('cursos.search');
    Route::get('cursos/creacion',                   [CursosController::class,'create'         ])->middleware(['permission:cursos.create'])->name('cursos.create');
    
    Route::post('cursos/creacion',                  [CursosController::class,'store'          ])->middleware(['permission:cursos.create'])->name('cursos.store');    
    Route::put('change/cursos/estatus/{id}',        [CursosController::class,'changeEstatusCursos'])->name('cursos.change.status');
    Route::get('cursos/creacion/editar/{idcurso}',  [CursosController::class,'edit'           ])->middleware(['permission:cursos.edit'])->name('cursos.edit');
    Route::put('cursos/creacion/update/{idcurso}',  [CursosController::class,'update'         ])->middleware(['permission:cursos.edit'])->name('cursos.update');   
    Route::delete('cursos/deleted/{idcurso}',       [CursosController::class,'destroy'        ])->middleware(['permission:cursos.delete'])->name('cursos.delete');
    
//// modulo cursos - asignacion participantes
    Route::get('cursos/asignacion/index',           [CursosController::class,'asignacionParticipantesIndex'])->middleware(['permission:cursos.asignacion'])->name('cursos.asignacion');
    Route::post('cursos/asignacion/index',          [CursosController::class,'asignacionParticipantesStore'])->middleware(['permission:cursos.asignacion'])->name('cursos.asignacion.store');
    
    // VER PARTICIPANTES POR LOS CURSOS
    Route::get('cursos/participantes/ver',                      [CursosController::class,'indexParticipantes'     ])->middleware(['permission:cursos.participantes.buscador'])->name('participantes.index');
    Route::post('cursos/participantes/ver/cursos',              [CursosController::class,'ShowParticipantesCursos'])->middleware(['permission:cursos.participantes.buscador'])->name('cursos.index.post');
    Route::get('cursos/participantes/view/{idcurso}',           [CursosController::class,'showParticipantes'      ])->middleware(['permission:cursos.participantes.buscador'])->name('participantes.showing');
    //MOSTAR PARTICIPANTES DE UN CURSO

    Route::get('participantes/buscador/',                       [CursosController::class,'buscarparticipante' ])->middleware(['permission:cursos.participantes.buscador'])->name('participante.buscador.index');
    Route::post('participantes/buscador/listado',               [CursosController::class,'SearchParticipante' ])->middleware(['permission:cursos.participantes.buscador'])->name('buscador.listado.participante');    
    Route::get('cursos/participantes/edit/{idparticipante}',    [CursosController::class,'editParticipante'   ])->middleware(['permission:cursos.editar.participantes'])->name('participantes.edit');
    Route::put('cursos/participantes/update/{id}',              [CursosController::class,'updateParticipante' ])->middleware(['permission:cursos.editar.participantes'])->name('participante.update');
    Route::delete('cursos/participantes/delete/{id}/',          [CursosController::class,'deleteParticipante' ])->middleware(['permission:cursos.eliminar.participantes'])->name('participante.destroy');
    //HISTORIAL DE PARTICIPANTE
    Route::post('cursos/participantes/historial/{id}',          [CursosController::class,'historial'          ])->middleware(['permission:cursos.participante.historial'])->name('participantes.historial');
    //// modulo cursos - Asistencia
    Route::get('cursos/asistencia/index',                       [CursosAsistencias::class,'Index'          ])->middleware(['permission:cursos.asistencia'])->name('cursos.asistencia.index');
    Route::post('cursos/asistencia/show/{id?}',                 [CursosAsistencias::class,'show'           ])->middleware(['permission:cursos.asistencia'])->name('cursos.asistencia.show');
    Route::post('cursos/asistencia/store',                      [CursosAsistencias::class,'store'          ])->middleware(['permission:cursos.asistencia'])->name('cursos.asistencia.store');
    
    //// modificacion de asistencias
    Route::get('cursos/asistencia/create/modifity',             [CursosAsistencias::class,'create'                 ])->middleware(['permission:cursos.asistencia.edit'])->name('cursos.asistencia.create');
    Route::post('cursos/asistencia/edit/participantes',         [CursosAsistencias::class,'ShowCursosAsistencia'   ])->name('cursos.asistencia.search');
    Route::get('cursos/asistencia/participantes/{id}',          [CursosAsistencias::class,'edit'                   ])->middleware(['permission:cursos.asistencia.edit'])->name('cursos.asistencia.edit');
    Route::PUT('cursos/asistencia/participantes/{id}',          [CursosAsistencias::class,'update'                 ])->middleware(['permission:cursos.asistencia.edit'])->name('cursos.asistencia.update');
    //// aprobacion participantes
    Route::get('cursos/aprobacion/participantes',                   [AprobacionesController::class,'index'            ])->middleware(['permission:cursos.aprobacion'])->name('cursos.aprobacion.index');
    Route::post('cursos/aprobacion/show/participantes/{curso?}/',   [AprobacionesController::class,'show'             ])->middleware(['permission:cursos.aprobacion'])->name('cursos.aprobacion.show');
    Route::post('cursos/selects/aprobacion/estatus',                [AprobacionesController::class,'create'           ])->middleware(['permission:cursos.aprobacion'])->name('cursos.aprobacion.create');
    Route::put('cursos/aprobacion/store/participantes/{curso}/',    [AprobacionesController::class,'store'            ])->middleware(['permission:cursos.aprobacion'])->name('cursos.aprobacion.store');
    
    //// LISTADO DE PRE-APROBACION
    Route::get('cursos/aprobacion/migracion',                       [AprobacionesController::class,'aprobacionParticipantesIndex' ])->middleware(['permission:cursos.migrar.participantes'])->name('cursos.migracion');
    Route::post('cursos/aprobacionlp/show/participantes/{curso?}/', [AprobacionesController::class,'showAprobadoslp'              ])->middleware(['permission:cursos.migrar.participantes'])->name('cursos.migracion.show');
    Route::put('cursos/aprobacion/store',                           [AprobacionesController::class,'aprobacionParticipantesStore' ])->middleware(['permission:cursos.migrar.participantes'])->name('cursos.lp.store');
    
    //// REPORTES CURSOS
    Route::get('cursos/reportes/participantes',                 [CursosReportes::class,'index'        ])->middleware(['permission:cursos.reportes'])->name('cursos.reportes.index');
    Route::post('cursos/reportes/participantes',                [CursosReportes::class,'create'       ])->middleware(['permission:cursos.reportes'])->name('cursos.reportes.create');

///////////////////////-------------------------------- ARC -------------------------/// 
    Route::get('arc/creacion',                          [ArcController::class,'create'       ])->middleware(['permission:arc.module'])->name('arc.create');
    Route::post('arc/creacion',                         [ArcController::class,'store'        ])->middleware(['permission:arc.module'])->name('arc.store');

///////////////////////--------------------------------ARI -------------------------///   
    Route::get('unidades/administracion/create/',       [AriController::class,'CreateUt'     ])->middleware(['permission:ari.module'])->name('generate.ut');
    Route::post('unidades/administracion/create/',      [AriController::class,'StoreUt'      ])->middleware(['permission:ari.module'])->name('ut.store');
    Route::get('unidades/administracion/',              [AriController::class,'ActiveUtIndex'])->middleware(['permission:ari.module'])->name('ut.index');
    Route::put('unidades/activacion/{id}/{sw?}',        [AriController::class,'ActiveUpdate' ])->middleware(['permission:ari.module'])->name('unidades.activacion.update');  
/////UNIDADES TRIBUTARIAS
    Route::get('unidades/editar/{id}/{anio}/{periodo}', [AriController::class,'edit'         ])->middleware(['permission:ari.module'])->name('ut.edit');    
    Route::put('unidades/editar/{id}/',                 [AriController::class,'update'       ])->middleware(['permission:ari.module'])->name('ut.update'); 
//GENERAR PLANILLAS ARI POR CEDULA 
    Route::get('generar/ari/',                          [AriController::class,'generateIndex'                        ])->middleware(['permission:ari.module'])->name('generate.ari');
    Route::post('generar/ari/',                         [AriController::class,'generate'                             ])->middleware(['permission:ari.module'])->name('generate.ari.planilla');
    Route::get('ari/generate/view/{historial}/{personal}/{anio}/{variacion}', [AriController::class,'GenerateAriView'])->middleware(['permission:ari.module'])->name('generate.ari.view');       
//REPORTE GENERAL ARI
    Route::get('ari/generate/reportes',                 [AriController::class,'reporte'          ])->middleware(['permission:ari.module'])->name('reporte.ari');
    Route::post('ari/generate/reportes/general',        [AriController::class,'ari_reporte'      ])->middleware(['permission:ari.module'])->name('generate.ari.reporte');

///////////////////////--------------------------------CONSTANCIAS -------------------------/// 
    Route::get('constancias/reporte',                   [Constancias::class,'index'            ])->middleware(['permission:constancias.reportes'])->name('constancias.reportes');
    Route::post('constancias/reporte/generar',          [Constancias::class,'generarReporte'   ])->middleware(['permission:constancias.reportes'])->name('constancias.reporte.generate');

///////////////////////--------------------------------ASISTENCIA OPERATIVA-------------------------/// 
    Route::get('asistencia/personal/operativa',                 [AsistenciaController::class,'index'              ])->name('asistencia.index');    
    Route::post('asistencia/personal/operativa/search',         [AsistenciaController::class,'create'             ])->name('asistencia.search.create');
    Route::post('asistencia/personal/operativa/sup/',           [AsistenciaController::class,'store'              ])->name('asistencia.store');
    Route::get('asistencia/personal/operativa/administracion',  [AsistenciaController::class,'indexAdministracion'])->middleware(['permission:operativa.diaria.view'])->name('asistencia.administracion');
    Route::post('asistencia/personal/operativa/administracion', [AsistenciaController::class,'showAdministracion' ])->name('asistencia.administracion.show');
    Route::get('asistencia/personal/operativa/{id}/edit',       [AsistenciaController::class,'edit'               ])->middleware(['permission:asistencia.operativa.editar'])->name('asistencia.administracion.edit');
    Route::PUT('asistencia/personal/operativa/{id}/update',     [AsistenciaController::class,'update'             ])->middleware(['permission:asistencia.operativa.editar'])->name('asistencia.administracion.update');
     
    Route::get('asistencia/personal/load/reposos',                      [AsistenciaController::class,'indexReposos'           ])->middleware(['permission:operativa.diaria.upload'])->name('asistencia.reposos.index');
    Route::post('asistencia/personal/load/reposos',                     [AsistenciaController::class,'createReposos'          ])->name('asistencia.reposos.create');
    
    Route::get('asistencia/personal/operativa/administracion/remove'  , [AsistenciaController::class,'AdministracionDelete'   ])->middleware(['permission:operativa.diaria.deleted'])->name('asistencia.remove');      
    Route::post('asistencia/personal/operativa/administracion/remove' , [AsistenciaController::class,'ShowDeleted'            ])->middleware(['permission:operativa.diaria.deleted'])->name('asistencia.administracion.show.group');     
    Route::delete('asistencia/personal/operativa/destroy/{sup}/{area}/{cartera}/{skill}/{dia}/{created_at}', [AsistenciaController::class,'AdministracionDestroy'])->middleware(['permission:operativa.diaria.deleted'])->name('asistencia.administracion.destroy');
  
    Route::get('asistencia/personal/reportes/asistencia',       [AsistenciaReportes::class,'index'    ])->name('asistencia.reporte.index');
    Route::post('asistencia/personal/reportes/asistencia',      [AsistenciaReportes::class,'create'   ])->name('asistencia.reporte.create');

    Route::get('asistencia/personal/reportes/indicadores',      [AsistenciaReportes::class,'indicadoresIndex' ])->name('asistencia.reporte.indicadores');
    Route::post('asistencia/personal/reportes/indicadores',     [AsistenciaReportes::class,'indicadoresCreate'])->name('asistencia.reporte.indicadores.create');

///////////////////////--------------------------------INVENTARIO-------------------------/// 
    Route::post('inventario/autocomplete',            [InventarioFinds::class,'autocompletes'               ])->name('inventario.autocompletes');
    Route::post('inventario/autocomplete/monitores',  [InventarioFinds::class,'autocompletesChangeMonitor'  ])->name('inventario.autocompletes.monitor');

    Route::post('inventario/select/areas',            [InventarioFinds::class,'SelectAreasPos'              ])->name('inventario.selectareas');
    Route::post('inventario/select/carteras',         [InventarioFinds::class,'SelectCarterasPos'           ])->name('inventario.selectcarteras');
    
    Route::get('inventario/stock/create',             [ComponentesController::class,'StockCreate'           ])->middleware(['permission:inventario.componentes.create'])->name('inventario.create.stock');
    Route::post('inventario/stock/create',            [ComponentesController::class,'StockStore'            ])->middleware(['permission:inventario.componentes.index'])->name('inventario.store.stock');
    
    Route::get('inventario/stock/index',              [ComponentesController::class,'StockIndex'            ])->middleware(['permission:inventario.componentes.index'])->name('inventario.index.stock');
    Route::post('inventario/stock/index',             [ComponentesController::class,'show'                  ])->middleware(['permission:inventario.componentes.index'])->name('inventario.index.show');
    
    Route::get('inventario/perifericos/create',     [PerifericosController::class,'create'                  ])->middleware(['permission:inventario.perifericos.create'])->name('inventario.create.periferico');
    Route::post('inventario/perifericos/create',    [PerifericosController::class,'store'                   ])->middleware(['permission:inventario.perifericos.index'])->name('inventario.store.periferico');
    
    Route::get('inventario/periferico/index',       [PerifericosController::class,'Index'                     ])->middleware(['permission:inventario.perifericos.index'])->name('inventario.index.periferico');
    Route::post('inventario/periferico/index',      [InventarioFinds::class,'AsignacionPerifericosPosicion'   ])->middleware(['permission:inventario.perifericos.index'])->name('inventario.show.periferico');
    
    Route::get('inventario/periferico/index/monitor',   [PerifericosController::class,'IndexMonitor'  ])->middleware(['permission:inventario.perifericos.monitores.index'])->name('inventario.index.monitor');
    Route::get('inventario/periferico/create/monitor',  [PerifericosController::class,'CreateMonitor' ])->middleware(['permission:inventario.perifericos.monitores.create'])->name('inventario.create.monitor');
    Route::post('inventario/periferico/create/monitor', [PerifericosController::class,'StoreMonitor'  ])->middleware(['permission:inventario.perifericos.monitores.create'])->name('inventario.store.monitor');

    Route::get('inventario/equipos/index',              [EquiposController::class,'index'                 ])->middleware(['permission:inventario.equipos.index'])->name('inventario.index.equipo');
    Route::post('inventario/equipos/show',              [EquiposController::class,'show'                  ])->middleware(['permission:inventario.equipos.index'])->name('inventario.show.equipo');
    
    Route::get('inventario/equipos/create',             [EquiposController::class,'create'                ])->middleware(['permission:inventario.equipos.create'])->name('inventario.create.equipo');
    Route::put('inventario/equipos/create',             [EquiposController::class,'ReplaceComponenteEquip'])->middleware(['permission:inventario.equipos.create'])->name('inventario.equipo.componente.update');
    Route::get('inventario/equipos/edit/{id}',          [EquiposController::class,'edit'                  ])->middleware(['permission:inventario.equipos.create'])->name('inventario.edit.equipo');
    
    Route::post('inventario/equipos/search/componente/',    [InventarioFinds::class,'SearchAddComponenteEquip'    ])->name('inventario.search.add.componente.equipo');
    Route::post('inventario/equipos/add/componente/{type}', [EquiposController::class,'AddNewComponente'          ])->middleware(['permission:inventario.equipos.create'])->name('inventario.add.componente.equipo');
    Route::post('inventario/equipos/componente/update',     [EquiposController::class,'store'                     ])->middleware(['permission:inventario.equipos.create'])->name('inventario.store.equipo');
    Route::put('inventario/equipos/update/{id}',            [EquiposController::class,'update'                    ])->middleware(['permission:inventario.equipos.create'])->name('inventario.update.equipo');
    Route::put('inventario/equipos/update/{id}/componente', [ComponentesController::class,'update'                ])->middleware(['permission:inventario.equipos.create'])->name('inventario.equipo.update.componente');

    Route::get('inventario/posiciones/create',                      [PosicionesController::class,'create'         ])->middleware(['permission:inventario.posiciones.create'])->name('inventario.create.posiciones');
    Route::post('inventario/posiciones/create',                     [PosicionesController::class,'store'          ])->middleware(['permission:inventario.posiciones.create'])->name('inventario.store.posiciones');
    
    Route::get('inventario/posiciones/index',                       [PosicionesController::class,'index'          ])->middleware(['permission:inventario.posiciones.index'])->name('inventario.index.posiciones');
    Route::post('inventario/posiciones/show',                       [PosicionesController::class,'show'           ])->middleware(['permission:inventario.posiciones.index'])->name('inventario.show.posiciones');    
    Route::get('inventario/posiciones/edit/{id}',                   [PosicionesController::class,'edit'           ])->middleware(['permission:inventario.posiciones.create'])->name('inventario.edit.posiciones');
    
    Route::put('inventario/posiciones/update/{type}/status/{id}',   [PosicionesController::class,'UpdateSyncStatus'               ])->name('inventario.posiciones.update.syncStatus');
    Route::put('inventario/posiciones/update/{id}/content/{type}',  [PosicionesController::class,'UpdateContentPosition'          ])->name('inventario.posiciones.update.Content');
    Route::post('inventario/posiciones/add/{type}/',                [PosicionesController::class,'AddNewPerifericoModalPosicion'  ])->name('inventario.add.perifericos');       
    Route::put('inventario/posiciones/{id}/update',                 [PosicionesController::class,'update'                         ])->middleware(['permission:inventario.posiciones.create'])->name('inventario.posiciones.update');
        
    Route::get('inventario/posiciones/migracion/create',            [MigracionController::class,'create'          ])->middleware(['permission:inventario.posiciones.migration.index'])->name('inventario.create.migracion');    
    Route::post('inventario/posiciones/migracion/searchs',          [MigracionController::class,'show'            ])->name('inventario.search.migracion');
    Route::post('inventario/posiciones/migracion/store',            [MigracionController::class,'store'           ])->middleware(['permission:inventario.posiciones.migration.create'])->name('inventario.store.migracion');
    
    Route::get('inventario/posiciones/migracion/index',             [MigracionController::class,'index'           ])->middleware(['permission:inventario.posiciones.migration.index'])->name('inventario.index.migracion');
    Route::post('inventario/posiciones/migracion/{id}/view',        [MigracionController::class,'ViewMigration'   ])->middleware(['permission:inventario.posiciones.migration.index'])->name('inventario.view.migracion');
    Route::put('inventario/posiciones/migracion/update/{id}',       [MigracionController::class,'update'          ])->name('inventario.update.migracion');
    
    /// tabla de totalizacion de compoentes por Stock para el registro de equipos
    Route::post('inventario/totales/componentes/equipos',       [InventarioFinds::class,'AsignacionComponentesEquipos'  ])->name('inventario.AsignacionComponentesEquipos');
    /// tabla de totalizacion de perifericos por Stock para el registro de equipos
    Route::post('inventario/totales/perifericos/equipos',       [InventarioFinds::class,'AsignacionPerifericosPosicion' ])->name('inventario.AsignacionPerifericosPosiciones');
    ///tabla de totalizacion de equipos
    Route::post('inventario/totales/equipos',                   [InventarioFinds::class,'AsignacionEquiposPosicion'     ])->name('inventario.AsignacionEquiposPosiciones');
    Route::post('inventario/mapas/sedes',                       [InventarioFinds::class,'GetMapSVGSede'                 ])->name('inventario.GetMapSVG');
    
    Route::get('inventario/administracion/reportes/index',      [InventarioReportes::class,'index'                      ])->middleware(['permission:inventario.administracion.reportes'])->name('inventario.migracion.administracion.reportes.index');
    Route::post('inventario/administracion/reportes/index',     [InventarioReportes::class,'store'                      ])->middleware(['permission:inventario.administracion.reportes'])->name('inventario.migracion.administracion.reportes.store');

    Route::get('inventario/administracion/upload/index',        [UploadsController::class,'index'                       ])->middleware(['permission:inventario.administracion.cargas'   ])->name('inventario.migracion.administracion.uploads.index');
    Route::post('inventario/administracion/upload/index',       [UploadsController::class,'store'                       ])->middleware(['permission:inventario.administracion.cargas'   ])->name('inventario.migracion.administracion.uploads.store');

    Route::get('inventario/administracion/etiquetado/index',    [EtiquetaController::class,'index'                      ])->middleware(['permission:inventario.administracion.etiquetas'])->name('inventario.migracion.administracion.etiqueta.index');
    Route::post('inventario/administracion/etiquetado/search',  [EtiquetaController::class,'search'                     ])->middleware(['permission:inventario.administracion.etiquetas'])->name('inventario.migracion.administracion.etiqueta.search');
    Route::post('inventario/administracion/etiquetado/create',  [EtiquetaController::class,'create'                     ])->middleware(['permission:inventario.administracion.etiquetas'])->name('inventario.migracion.administracion.etiqueta.create');
    
    ///////////////////////---------------------------------------------EVALUACIONES DE PERSONAL ------------------------------------------------///     
    Route::get('evaluaciones/administracion/index/',            [EvaluacionesController::class,'index'    ])->middleware(['permission:evaluaciones.administracion.create' ])->name('evaluacion.administracion.index');
    Route::post('evaluaciones/administracion/show/',            [EvaluacionesController::class,'show'     ])->name('evaluacion.administracion.show');
    Route::get('evaluaciones/administracion/edit/{id}',         [EvaluacionesController::class,'edit'     ])->middleware(['permission:evaluaciones.administracion.edit'   ])->name('evaluacion.administracion.edit');
    Route::put('evaluaciones/administracion/update/{id}',       [EvaluacionesController::class,'update'   ])->middleware(['permission:evaluaciones.administracion.edit'   ])->name('evaluacion.administracion.update');
    Route::delete('evaluaciones/administracion/destroy/{id}',   [EvaluacionesController::class,'destroy'  ])->middleware(['permission:evaluaciones.administracion.delete' ])->name('evaluacion.administracion.destroy');
    Route::put('segmentos/activacion/{id}/',                    [EvaluacionesController::class,'ActiveUpdate'])->name('segment.change.estatus');
    
    Route::get('evaluaciones/administracion/create/',           [EvaluacionesController::class,'create'   ])->middleware(['permission:evaluaciones.administracion.create' ])->name('evaluacion.administracion.create');
    Route::post('evaluaciones/administracion/search/cargos',    [EvaluacionFinds::class,'SearchCargos'    ])->name('evaluacion.buscador.cargos');
    Route::post('evaluaciones/administracion/create',           [EvaluacionesController::class,'Store'    ])->middleware(['permission:evaluaciones.administracion.create' ])->name('evaluacion.administracion.store');
    
    Route::get('evaluaciones/create/',                          [EvaluacionesController::class,'NewEvaluacion'    ])->middleware(['permission:evaluaciones.create'])->name('evaluacion.create');
    Route::post('evaluaciones/cargos/permisos',                 [EvaluacionFinds::class,'CargosPermisos'          ])->name('cargos.evaluar.permisos');
    Route::post('evaluaciones/create/personal/',                [EvaluacionesController::class,'consultEvaluacion'])->name('evaluacion.consult.personal');
    
    Route::post('evaluaciones/periodo/bring',                   [EvaluacionFinds::class,'getPeriodo'              ])->name('bring.periodo');
    Route::post('evaluaciones/periodo/semana',                  [EvaluacionesController::class,'getSemana'        ])->name('selector.semana');
    Route::post('evaluaciones/semanas',                         [EvaluacionesController::class,'selectorSemanas'  ])->name('selector.semanascompletas');
    
    Route::post('evaluaciones/create/personal/bring/{skill_asig}/{cargo_eval}/{fecha_evaluacion}/{tipo_periodo}/cartera/{cartera}/area/{area}', 
                                                                [EvaluacionesController::class,'getPersonal'              ])->name('evaluacion.bring.personal'); 
    Route::post('evaluaciones/register',                        [EvaluacionesController::class,'register'                 ])->middleware(['permission:evaluaciones.create'])->name('evaluacion.register');
           
    Route::get('evaluaciones/edit/',                            [EvaluacionesController::class,'EditEvaluacion'           ])->middleware(['permission:evaluaciones.edit'])->name('evaluacion.edit');
    Route::post('evaluaciones/edit/personal/',                  [EvaluacionesController::class,'consultEditEvaluacion'    ])->middleware(['permission:evaluaciones.edit'])->name('evaluacion.edit.personal');
    
    Route::post('evaluaciones/edit/personal/bring/{evaluacion}',[EvaluacionesController::class,'GetPersonalEdit'          ])->name('edit.bring.personal');
    Route::post('evaluaciones/update',                          [EvaluacionesController::class,'updateEvaluacion'         ])->middleware(['permission:evaluaciones.edit'])->name('evaluacion.update');        
    
    Route::get('evaluaciones/reporte/',                         [EvaluacionesController::class,'reporteEvaluacion'        ])->middleware(['permission:evaluaciones.reportes'])->name('evaluacion.reporte');
    Route::post('evaluaciones/reporte/process/',                [EvaluacionesController::class,'getReporte'               ])->middleware(['permission:evaluaciones.reportes'])->name('evaluacion.reporte.download');
        
    Route::get('evaluaciones/masiva/',                          [EvaluacionesController::class,'cargaIndex'          ])->middleware(['permission:evaluaciones.create.masive'])->name('evaluacion.masiva.index');
    Route::post('evaluaciones/masiva/process/',                 [EvaluacionesController::class,'cargaProcess'        ])->name('evaluacion.masiva.process');
    Route::post('evaluaciones/masiva/generar/formato',          [EvaluacionesController::class,'generarFormato'      ])->middleware(['permission:evaluaciones.create.masive'])->name('generar.formato');     

    
///////////////////////--------------------------------COMISIONES Y VARIABLES -------------------------///      
    Route::get('comisiones/upload/',                            [ComisionesController::class,'index'                      ])->middleware(['permission:comisiones.import.export'])->name('comisiones.index');
    Route::post('determinacion/tipo/pago',                      [ComisionesController::class,'getTipoPago'                ])->name('determinar.tipo.pago');
    Route::post('comisiones/selector/previouscurrentweek/',     [ComisionesController::class,'getPreviousAndCurrentWeek'  ])->name('selector.previouscurrent');
    Route::post('comisiones/selector/corte/',                   [ComisionesController::class,'getCorte'                   ])->name('selector.corte');    
    Route::post('comisiones/upload/process',                    [ComisionesController::class,'store'                      ])->middleware(['permission:comisiones.import.export','throttle:1,1'])->name('comisiones.store');
    
    Route::get('comisiones/auditoria/',                         [ComisionesController::class,'auditoria'              ])->middleware(['permission:comisiones.auditoria.pagos'])->name('comisiones.auditoria');
    Route::post('comisiones/auditoria/list',                    [ComisionesController::class,'auditoriaList'          ])->middleware(['permission:comisiones.auditoria.pagos'])->name('comisiones.auditoria.list');
    Route::post('comisiones/auditoria/procesar/',               [ComisionesController::class,'auditoriaProcess'       ])->middleware(['permission:comisiones.auditoria.pagos'])->name('com.procesar.auditoria');
    Route::get('comisiones/auditoria/ver/{id}',                 [ComisionesController::class,'auditoriaView'          ])->middleware(['permission:comisiones.auditoria.pagos'])->name('com.auditoria.view');   
    Route::delete('lote/delete/{id}/',                          [ComisionesController::class,'loteDestroy'            ])->middleware(['permission:comisiones.auditoria.pagos'])->name('lote.destroy');
    Route::put('lote/reverse/{id}/',                            [ComisionesController::class,'loteReverse'            ])->middleware(['permission:comisiones.auditoria.pagos'])->name('lote.reverse');

    ///genera el consolidado de bancos
    Route::get('comisiones/payment/',                           [ComisionesController::class,'payment'                ])->middleware(['permission:comisiones.consolidado.bancario'])->name('comisiones.payment');
    Route::post('comisiones/payment/list',                      [ComisionesController::class,'paymentList'            ])->middleware(['permission:comisiones.consolidado.bancario'])->name('comisiones.payment.list');
    Route::post('comisiones/payment/procesar/',                 [ComisionesController::class,'paymentProcess'         ])->middleware(['permission:comisiones.consolidado.bancario'])->name('com.procesar.pagos');
    
    ///pestaña Pagar
    Route::get('comisiones/banco/',                             [ComisionesController::class,'bank'                  ])->middleware(['permission:comisiones.consolidado.pagar'])->name('comisiones.bank');
    Route::post('comisiones/bank/estatus/',                     [ComisionesController::class,'bankShowList'          ])->middleware(['permission:comisiones.consolidado.pagar'])->name('comisiones.bank.change.estatus');
    Route::get('comisiones/bank/export/{id}',                   [ComisionesController::class,'bankExport'            ])->middleware(['permission:comisiones.consolidado.banco'])->name('comisiones.bank.export');
    Route::delete('lotebanco/delete/{id}/',                     [ComisionesController::class,'lotebancoDestroy'      ])->middleware(['permission:comisiones.consolidado.pagos'])->name('lotebanco.destroy');
    Route::post('comisiones/bank/estatus/changing',             [ComisionesController::class,'changingConsolidadoPay'])->middleware(['permission:comisiones.consolidado.pagos'])->name('comisiones.bank.changing');
    
    //pestaña Cargar incidencias   
    Route::post('comisiones/bank/consult/',                     [ComisionesController::class,'bankConsultIncidencias'     ])->name('comisiones.bank.consult');            
    Route::get('comisiones/bank/observaciones/{id}',            [ComisionesController::class,'observacionesExport'        ])->name('comisiones.bank.observaciones');
    Route::put('lote/discard/{id}/'   ,                         [ComisionesController::class,'loteBankIncidenciasDiscard' ])->middleware(['permission:comisiones.consolidado.observaciones'])->name('lote.banco.discard');
    Route::get('comisiones/estatus/incidencia/{id}',            [ComisionesController::class,'personalIncidencia'         ])->middleware(['permission:comisiones.consolidado.observaciones'])->name('personal.incidencia');        
    Route::put('comisiones/estatus/incidencia/change/',         [ComisionesController::class,'changeIncidencia'           ])->middleware(['permission:comisiones.consolidado.observaciones'])->name('change.incidencia');
    Route::post('comisiones/bank/process/',                     [ComisionesController::class,'bankProcessIncidencias'     ])->middleware(['permission:comisiones.consolidado.observaciones'])->name('comisiones.bank.incidencias');    
    Route::post('comisiones/report/bank/',                      [ComisionesController::class,'bankReport'                 ])->middleware(['permission:comisiones.consolidado.banco'        ])->name('comisiones.bank.report');
    
    /////REPORTE COMISIONES
    Route::get('comisiones/export/',                            [ComisionesController::class,'ReportesExport'             ])->middleware(['permission:comisiones.import.export'        ])->name('comisiones.export');
    Route::post('determinacion/semanas',                        [ComisionesController::class,'GetSemanas'                 ])->name('get.semanas');
    Route::post('comisiones/selector/tipoperiodo/',             [ComisionesController::class,'selectorPeriodos'           ])->name('selector.tipoperiodo');
    Route::post('comisiones/export/process',                    [ComisionesController::class,'exportProcess'              ])->middleware(['permission:comisiones.import.export'        ])->name('comisiones.export.process');
        
    Route::get('comisiones/reporte/incidencias/cargadas',       [ComisionesController::class,'Incidencias'                ])->middleware(['permission:comisiones.incidencia.reporte'   ])->name('comisiones.incidencias.reporte');
    Route::post('comisiones/reporte/incidencias/cargadas/show', [ComisionesController::class,'IncidenciasShow'            ])->middleware(['permission:comisiones.incidencia.reporte'   ])->name('comisiones.incidencias.show');        
          
    
    ///////////////////////--------------------------------TALENTO HUMANO -> HABILIDADES BLANDAS -------------------------///     
    Route::get('habilidades/evaluaciones/create',           [HabilidadController::class,'createEvaluacion'   ])->middleware(['permission:habilidades.blandas.create'        ])->name('habilidades.evaluacion.create');                           
    Route::post('habilidades/evaluaciones/create/save',     [HabilidadController::class,'storeEvaluacion'    ])->name('habilidades.evaluacion.store');   
    Route::get('habilidades/evaluaciones/edit',             [HabilidadController::class,'editEvaluacion'     ])->middleware(['permission:habilidades.blandas.edit'           ])->name('habilidades.evaluacion.edit');
    Route::put('evaluaciones/habilidad/activacion/{id}/' ,  [HabilidadController::class,'ActiveUpdate'       ])->name('eval.change.estatus');            
    Route::delete('habilidades/evaluacion/delete/{id}/',    [HabilidadController::class,'destroyEvaluacion'  ])->middleware(['permission:habilidades.blandas.deleted'        ])->name('test.destroy');
    Route::get('habilidades/edit/eval/{id}',                [HabilidadController::class,'habEdit'            ])->middleware(['permission:habilidades.blandas.edit'           ])->name('hab.edit');
    Route::put('habilidades/evaluacion/edit/{id}/update',   [HabilidadController::class,'updateEvaluacion'   ])->name('habilidades.evaluacion.update');

    Route::get('habilidades/evaluaciones/preguntas/create',                         [HabilidadController::class,'createPreguntas'    ])->middleware(['permission:habilidades.blandas.create'        ])->name('habilidades.preguntas.create');
    Route::post('habilidades/evaluaciones/preguntas/categoria',                     [HabilidadController::class,'getCategoria'       ])->name('check.categoria');
    Route::get('habilidades/evaluaciones/{evaluacion_id}/categoria/{categoria_id}' ,[HabilidadController::class,'getPreguntasList'   ])->name('check.pregunta.list');
    Route::PUT('habilidades/evaluaciones/preguntas/save',                           [HabilidadController::class,'storePreguntas'     ])->middleware(['permission:habilidades.blandas.create'        ])->name('habilidades.preguntas.store');

    Route::get('habilidades/evaluaciones/respuestas/create',                [HabilidadController::class,'createRespuestas'            ])->middleware(['permission:habilidades.blandas.create'   ])->name('habilidades.respuestas.create');
    Route::post('habilidades/evaluaciones/respuestas/categoria',            [HabilidadController::class,'SearchCategoriaforRespuestas'])->name('search.categoria.respuestas');
    Route::post('habilidades/evaluaciones/preguntas/categoria/consult',     [HabilidadController::class,'consultPreguntas'            ])->name('consult.pregunta');        
    Route::get('habilidades/respuestas/{eval_id}/cat/{cat_id}/{preg_id}',   [HabilidadController::class,'getRespuestasList'           ])->name('check.respuesta.list');
    Route::PUT('habilidades/evaluaciones/respuestas/save',                  [HabilidadController::class,'storeRespuestas'             ])->name('habilidades.respuestas.store');
    Route::get('habilidades/evaluacion/reportes',                           [HabilidadController::class,'indexReportes'  ])->middleware(['permission:habilidades.blandas.reportes'                   ])->name('habilidades.reportes');
    Route::post('habilidades/evaluacion/getpersonal',                       [HabilidadController::class,'getPersonal'    ])->name('search.personal.cargo');
    Route::post('habilidades/evaluacion/getcargo',                          [HabilidadController::class,'getCargo'       ])->name('cargo.historico');     
    Route::post('habilidades/generar/grafico',                              [HabilidadController::class,'getGrafico'     ])->name('generar.grafico');        
    Route::post('habilidades/evaluacion/reportes/generar',                  [HabilidadController::class,'getReporte'     ])->name('habilidades.reportes.generar');       

    ///////////////////////--------------------------------RECLUTAMIENTO -------------------------///     

    Route::get('reclutamiento/preseleccion/candidatos'               ,[ReclutamientoController::class,'PreseleccionCandidatos'     ])->middleware(['permission:reclutamiento.preseleccion.candidatos'   ])->name('reclutamiento.preseleccion');    
    Route::post('reclutamiento/preseleccion/candidatos'              ,[ReclutamientoController::class,'ShowPreseleccionCandidatos' ])->middleware(['permission:reclutamiento.preseleccion.candidatos'   ])->name('reclutamiento.Showpreseleccion');    

    Route::get('reclutamiento/estacion/principal'                    ,[ReclutamientoController::class,'estacion1'             ])->middleware(['permission:reclutamiento.estacion.listado'])->name('reclutamiento.estacion.principal');    
    Route::post('reclutamiento/{id}/prospecto/'                      ,[ReclutamientoController::class,'ShowProspecto'         ])->name('reclutamiento.estacion.showProspecto');    
    Route::delete('reclutamiento/delete/{id}/prospecto/'             ,[ReclutamientoController::class,'DeleteProspecto'       ])->middleware(['permission:reclutamiento.estacion.deleted'])->name('reclutamiento.estacion.Deleteprospecto');    
    Route::put('reclutamiento/update/{id}/prospecto/'                ,[ReclutamientoController::class,'updateProspecto'       ])->name('reclutamiento.estacion.updateProspecto');    
    Route::post('reclutamiento/{id}/acept/cursos/'                   ,[ReclutamientoController::class,'paseToCursoCandidato'  ])->name('reclutamiento.estacion.paseaCurso');    

    Route::get('reclutamiento/reporteria'                            ,[ReclutamientoReportes::class,'index'                       ])->middleware(['permission:reclutamiento.reportes'])->name('reclutamiento.reportes');    
    Route::post('reclutamiento/reporteria'                           ,[ReclutamientoReportes::class,'store'                       ])->middleware(['permission:reclutamiento.reportes'])->name('reclutamiento.reportes.download');    

    Route::get('reclutamiento/planillapreempleo/{id}/prospecto/'     ,[ReclutamientoController::class,'GeneratePdfProspecto'   ])->middleware(['permission:reclutamiento.estacion.listado'])->name('reclutamiento.estacion.pdfProspecto');    

    Route::post('logout',       [Logout::class, 'logout'])->name('logout');
    Route::get('logout/force',  [Logout::class, 'logout'])->name('logout.force');
});