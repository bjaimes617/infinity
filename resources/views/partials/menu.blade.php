<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        <img src="{{ Auth()->user()->RelationToPersonal != null ? \Storage::disk('personal')->url(\Auth()->user()->RelationToPersonal->image) : asset('img/user.png') }}"
            class="img-circle elevation-2" alt="User Image">
    </div>
    <div class="info">
        <a href="#" class="d-block">{{ \Auth::user()->name }}</a>
    </div>
</div>
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent" data-widget="treeview" role="menu"
        data-accordion="false">
        <!--GESTION PERSONAL -->
        @permission('cursos.module | personal.module | operativa.diaria.module')
            <li class="nav-item @yield('gestion_personal')">
                <a href="#" class="nav-link @yield('gestion_personal_active')">
                    <i class="nav-icon fas fa-user-cog"></i>
                    <p>Gestion de Personal<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <!--CURSOS-->
                    @permission('cursos.module | cursos.aprobacion | cursos.reportes
                        |cursos.ver.participantes|cursos.participante.historial|
                        cursos.eliminar.participantes|cursos.editar.participantes | cursos.create | cursos.edit |
                        cursos.delete | cursos.asignacion | cursos.migrar.participantes')
                        <li class="nav-item @yield('cursosmenu')">
                            <a href="#" class="nav-link @yield('cursosactive')">
                                <i class="nav-icon fas fa-certificate"></i>
                                <p>Cursos <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('cursos.asistencia')
                                    <li class="nav-item">
                                        <a href="{{ route('cursos.asistencia.index') }}" class="nav-link @yield('cursos.asistencia')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Asistencia</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('cursos.asistencia.edit')
                                    <li class="nav-item">
                                        <a href="{{ route('cursos.asistencia.create') }}" class="nav-link @yield('cursos.asistencia.create')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Modificar</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('cursos.aprobacion')
                                    <li class="nav-item">
                                        <a href="{{ route('cursos.aprobacion.index') }}" class="nav-link @yield('cursos.aprobacion.index')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Aprobar</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('cursos.ver.participantes|cursos.participante.historial|
                                    cursos.eliminar.participantes|cursos.editar.participantes')
                                    <li class="nav-item @yield('menu.cursos.participantes')">
                                        <a href="#" class="nav-link @yield('menu.cursos.participantes.active')">
                                            <i class="fa fa-user nav-icon"></i>
                                            <p> Participantes<i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('cursos.ver.participantes')
                                                <li class="nav-item">
                                                    <a href="{{ route('participantes.index') }}" class="nav-link @yield('participantes.index')">
                                                        <i class="far fa-circle nav-icon"></i>
                                                        <p>Por Cursos</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('cursos.participante.historial|cursos.eliminar.participantes|cursos.editar.participantes')
                                                <li class="nav-item">
                                                    <a href="{{ route('participante.buscador.index') }}"
                                                        class="nav-link @yield('buscar.index.participantes')">
                                                        <i class="far fa-circle nav-icon"></i>
                                                        <p>Buscar</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                                @permission('cursos.create | cursos.edit | cursos.delete | cursos.asignacion |
                                    cursos.migrar.participantes')
                                    <li class="nav-item @yield('menu.cursos.administracion')">
                                        <a href="#" class="nav-link @yield('menu.cursos.administracion.active')">
                                            <i class="fa fa-wrench nav-icon"></i>
                                            <p> Administración<i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('cursos.create')
                                                <li class="nav-item">
                                                    <a href="{{ route('cursos.create') }}" class="nav-link @yield('cursos.create')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Crear Cursos</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('cursos.edit|cursos.delete')
                                                <li class="nav-item">
                                                    <a href="{{ route('cursos.index') }}" class="nav-link @yield('cursos.index')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Ver Cursos</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('cursos.asignacion')
                                                <li class="nav-item">
                                                    <a href="{{ route('cursos.asignacion') }}" class="nav-link @yield('cursos.asignacion')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Asigna Participantes</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('cursos.migrar.participantes')
                                                <li class="nav-item">
                                                    <a href="{{ route('cursos.migracion') }}" class="nav-link @yield('cursos.aprobacion')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Migrar Participantes</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                                @permission('cursos.reportes')
                                    <li class="nav-item">
                                        <a href="{{ route('cursos.reportes.index') }}" class="nav-link @yield('cursos.reportes.index')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Reportes</p>
                                        </a>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission

                    <!--MODULO DE PERSONAL-->
                    <!--Si hay menu desplegables dentro, deben ser declarados en este permission-->
                    @permission('personal.module | personal.control.qr | control.visitantes.cargar |
                        control.recepciones.autana |
                        control.recepciones.macaracuay | control.recepciones.plaza | personal.reporte.listado |
                        personal.reporte.historicoegresado | personal.reporte.actualizacion | control.reportes |
                        control.reporte.participantescursos |
                        control.reportes')
                        <li class="nav-item @yield('menu_personal')">
                            <a href="#" class="nav-link @yield('personal')">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Expedientes <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('personal.index')
                                    <li class="nav-item">
                                        <a href="{{ route('personal.index') }}" class="nav-link @yield('personal.list')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Registrados</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('personal.create')
                                    <li class="nav-item">
                                        <a href="{{ route('personal.create') }}" class="nav-link @yield('personal.add')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Agregar</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('personal.import.masive')
                                    <li class="nav-item">
                                        <a href="{{ route('personal.upload.masive') }}" class="nav-link @yield('personal.upload')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Importar</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('personal.update.masive')
                                    <li class="nav-item">
                                        <a href="{{ route('personal.update.masive') }}" class="nav-link @yield('personal.update')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Actualización Masiva</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('personal.control.qr | control.visitantes.cargar | control.recepciones.autana |
                                    control.recepciones.macaracuay | control.recepciones.plaza')
                                    <li class="nav-item @yield('control.acceso')">
                                        <a href="#" class="nav-link @yield('acceso')">
                                            <i class="fas fa-user-secret nav-icon"></i>
                                            <p> Control de Acceso<i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('personal.control.qr')
                                                <li class="nav-item">
                                                    <a href="{{ route('control.qr') }}" class="nav-link @yield('control.qr')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>QR</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('control.visitantes.cargar')
                                                <li class="nav-item">
                                                    <a href="{{ route('personal.visitantes.masive') }}"
                                                        class="nav-link @yield('personal.visitantes.masive')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Visitantes</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('control.recepciones.autana | control.recepciones.macaracuay |
                                                control.recepciones.plaza')
                                                <li class="nav-item @yield('control.recepciones')">
                                                    <a href="#" class="nav-link">
                                                        <i class="fas fa-bullhorn nav-icon"></i>
                                                        <p> Recepciones <i class="right fas fa-angle-left"></i></p>
                                                    </a>
                                                    <ul class="nav nav-treeview">
                                                        @permission('control.recepciones.autana')
                                                            <li class="nav-item">
                                                                <a href="{{ route('control.scanner', 'autana') }}"
                                                                    class="nav-link @yield('control.recepciones.autana')">
                                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                                    <p>Autana</p>
                                                                </a>
                                                            </li>
                                                        @endpermission
                                                        @permission('control.recepciones.plaza')
                                                            <li class="nav-item">
                                                                <a href="{{ route('control.scanner', 'plaza') }}"
                                                                    class="nav-link @yield('control.recepciones.autana')">
                                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                                    <p>Plaza V</p>
                                                                </a>
                                                            </li>
                                                        @endpermission
                                                        @permission('control.recepciones.macaracuay')
                                                            <li class="nav-item">
                                                                <a href="{{ route('control.scanner', 'macaracuay') }}"
                                                                    class="nav-link @yield('control.recepciones.autana')">
                                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                                    <p>Macaracuay</p>
                                                                </a>
                                                            </li>
                                                        @endpermission
                                                    </ul>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                                <!--Sub modulo de reportes-->
                                @permission('personal.reporte.listado | personal.reporte.historicoegresado |
                                    personal.reporte.actualizacion
                                    | control.reportes | control.reporte.participantescursos | control.reportes')
                                    <li class="nav-item @yield('personal.reportes')">
                                        <a href="#" class="nav-link  @yield('reportes')">
                                            <i class="far fa-file nav-icon"></i>
                                            <p> Reportes <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('personal.reporte.listado')
                                                <li class="nav-item">
                                                    <a href="{{ route('personal.reporte.index') }}"
                                                        class="nav-link @yield('reportes.list')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Listado</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('personal.reporte.historicoegresado')
                                                <li class="nav-item">
                                                    <a href="{{ route('personal.historico.create') }}"
                                                        class="nav-link @yield('historico.egresados')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Listado Egresados</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('personal.reporte.actualizacion')
                                                <li class="nav-item">
                                                    <a href="{{ route('reporte.actualizacion') }}"
                                                        class="nav-link @yield('reporte.actualizacion')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Hist. Actualizaciones</p>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{ route('reporte.historico') }}" class="nav-link @yield('reporte.historico')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Historico de Expe.</p>
                                                    </a>
                                                </li>                                                                                                                                              
                                            @endpermission
                                            @permission('personal.reporte.replicas')
                                            <li class="nav-item">
                                                <a href="{{ route('reporte.replicas') }}" class="nav-link @yield('reporte.replicas')">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>Replica de Expe.</p>
                                                </a>
                                            </li>
                                            @endpermission
                                            @permission('control.reportes|control.reporte.participantescursos')
                                                <li class="nav-item">
                                                    <a href="{{ route('personal.reporte.accesscontrol') }}"
                                                        class="nav-link @yield('reportes.accesscontrol')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Control de Acceso</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                                <!--Aqui termina el sub menu de reportes-->
                            </ul>
                        </li>
                    @endpermission

                    <!--ASISTENCIA OPERATIVA-->
                    @permission('operativa.diaria.module')
                        <li class="nav-item @yield('menu_asistencia')">
                            <a href="#" class="nav-link @yield('asistencia')">
                                <i class="nav-icon fas fa-file-signature"></i>
                                <p>Asistencia Operativa <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('operativa.diaria.create')
                                    <li class="nav-item">
                                        <a href="{{ route('asistencia.index') }}" class="nav-link @yield('asistencia.index')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Diaria</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('operativa.diaria.view')
                                    <li class="nav-item">
                                        <a href="{{ route('asistencia.administracion') }}" class="nav-link @yield('asistencia.view')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Modificación</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('operativa.diaria.deleted | operativa.diaria.upload')
                                    <li class="nav-item @yield('menu_administracion')">
                                        <a href="#" class="nav-link @yield('asistencia.administracion')">
                                            <i class="fas fa-circle nav-icon"></i>
                                            <p> Administracion <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">

                                            @permission('operativa.diaria.deleted')
                                                <li class="nav-item">
                                                    <a href="{{ route('asistencia.remove') }}" class="nav-link @yield('asistencia.administracion.remove')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Eliminación</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('operativa.diaria.upload')
                                                <li class="nav-item">
                                                    <a href="{{ route('asistencia.reposos.index') }}"
                                                        class="nav-link @yield('asistencia.administracion.reposos')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Importar</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                                @permission('operativa.diaria.reportes | operativa.diaria.reportes_indicadores')
                                    <li class="nav-item @yield('asistencia.reporte.menu')">
                                        <a href="#" class="nav-link @yield('asistencia.reporte')">
                                            <i class="far fa-file nav-icon"></i>
                                            <p> Reporteria<i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('operativa.diaria.reportes')
                                                <li class="nav-item">
                                                    <a href="{{ route('asistencia.reporte.index') }}"
                                                        class="nav-link @yield('asistencia.reporte.index')">
                                                        <i class="far fa-circle nav-icon"></i>
                                                        <p>Reporte de Asistencia</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('operativa.diaria.reportes_indicadores')
                                                <li class="nav-item">
                                                    <a href="{{ route('asistencia.reporte.indicadores') }}"
                                                        class="nav-link @yield('asistencia.reporte.indicadores')">
                                                        <i class="far fa-circle nav-icon"></i>
                                                        <p>Indicadores</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission
                </ul>
            </li>
        @endpermission
        <!--RECURSOS HUMANOS -->
        @permission('arc.module | ari.module | constancias.module | recibos.module')
            <li class="nav-item @yield('recursos_humanos')">
                <a href="#" class="nav-link @yield('recursos_humanos_active')">
                    <i class="nav-icon fas fa-people-arrows"></i>
                    <p>Talento Humano<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <!--RECIBOS DE PAGOS-->
                    @permission('recibos.module | recibos.creacion.gerentes')
                        <li class="nav-item @yield('menurecibo')">
                            <a href="#" class="nav-link @yield('reciboactive')">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>Recibos de Pago <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('recibos.creacion.gerentes')
                                    <li class="nav-item">
                                        <a href="{{ route('recibos.creacion.index') }}" class="nav-link @yield('recibos.creacion.index')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Generar</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('recibos.administrador | recibos.activacion.periodos | recibos.administrador.reportes')
                                    <li class="nav-item @yield('menu.recibos.administracion')">
                                        <a href="#" class="nav-link @yield('recibos.administracion')">
                                            <i class="fas fa-eye nav-icon"></i>
                                            <p>Administración<i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('recibos.activacion.periodos')
                                                <li class="nav-item">
                                                    <a href="{{ route('recibos.activacion.index') }}"
                                                        class="nav-link @yield('recibos.activacion.index')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Activar Periodos</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('recibos.administrador')
                                                <li class="nav-item">
                                                    <a href="{{ route('recibos.administracion.index') }}"
                                                        class="nav-link @yield('recibos.administracion.index')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Gen. Cedula</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('recibos.administrador.reportes')
                                                <li class="nav-item">
                                                    <a href="{{ route('recibos.administracion.reporte.index') }}"
                                                        class="nav-link @yield('recibos.administracion.reporte.index')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Reporte Nominas</p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                @endpermission
                                @permission('recibos.ajustes.items')
                                    <li class="nav-item @yield('menu.recibos.ajustes')">
                                        <a href="#" class="nav-link @yield('recibos.ajustes')">
                                            <i class="fas fa-cog  nav-icon"></i>
                                            <p> Ajustes del Modulo<i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('recibos.items.index') }}" class="nav-link @yield('recibos.items')">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>Items Asig. y Deduc.</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission
                    <!--FIN RECIBOS DE PAGOS-->
                    <!--ARC-->
                    @permission('arc.module')
                        <li class="nav-item @yield('menu_arc')">
                            <a href="#" class="nav-link @yield('arc')">
                                <i class="nav-icon fas fas fa-file-pdf"></i>
                                <p>ARC <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('arc.create') }}" class="nav-link @yield('arc.generate')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Gen. Cedula</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endpermission
                    <!--FIN ARC-->
                    <!--ARI-->
                    @permission('ari.module')
                        <li class="nav-item @yield('menu_ari')">
                            <a href="#" class="nav-link @yield('ari')">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>ARI <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('generate.ut') }}" class="nav-link @yield('generate.ut')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Periodo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('ut.index') }}" class="nav-link @yield('ut.index')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Activar Periodo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('generate.ari') }}" class="nav-link @yield('generate.ari')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Visualizar ARI</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reporte.ari') }}" class="nav-link @yield('generate.reporte')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reporte General</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endpermission
                    <!--FIN ARI-->
                    <!--CONTANCIAS-->
                    @permission('constancias.module | constancias.reportes')
                        <li class="nav-item @yield('menu_constancias')">
                            <a href="#" class="nav-link @yield('constancias')">
                                <i class="nav-icon fas fa-file"></i>
                                <p>Constancias <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('constancias.reportes')
                                    <li class="nav-item">
                                        <a href="{{ route('constancias.reportes') }}" class="nav-link @yield('constancias.reportes')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Reportes</p>
                                        </a>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission
                    <!--FIN CONTANCIAS-->

                    <li class="nav-item @yield('menu_habilidades')">
                        <a href="#" class="nav-link @yield('habilidades')">
                            <i class="nav-icon fas fa-file"></i>
                            <p>Eval. Habilidades <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('habilidades.evaluacion.create') }}"
                                    class="nav-link @yield('habilidades.evaluacion.create')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Añadir </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('habilidades.evaluacion.edit') }}" class="nav-link @yield('habilidades.evaluacion.editar')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ediciones </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('habilidades.preguntas.create') }}"
                                    class="nav-link @yield('evaluaciones.habilidades.preguntas')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Preguntas </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('habilidades.respuestas.create') }}"
                                    class="nav-link @yield('evaluaciones.habilidades.respuestas')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Respuestas </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('habilidades.reportes') }}" class="nav-link @yield('evaluaciones.habilidades.reportes')">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reportes </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        @endpermission
        <!--COMISIONES Y VARIABLES -->
        @permission('comisiones.module')
            <li class="nav-item @yield('menu_comisiones')">
                <a href="#" class="nav-link @yield('comisiones')">
                    <i class="nav-icon fas fa-money-check-alt"></i>
                    <p>Variables/Comisiones<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @permission('comisiones.import.export')
                        <li class="nav-item">
                            <a href="{{ route('comisiones.index') }}" class="nav-link @yield('comisiones.import')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Importar </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('comisiones.auditoria.pagos')
                        <li class="nav-item">
                            <a href="{{ route('comisiones.auditoria') }}" class="nav-link @yield('comisiones.auditoria')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Auditoria </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('comisiones.consolidado.pagar')
                        <li class="nav-item">
                            <a href="{{ route('comisiones.payment') }}" class="nav-link @yield('comisiones.payment')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Procesar Pagos</p>
                            </a>
                        </li>
                    @endpermission
                    @permission('comisiones.consolidado.observaciones | comisiones.consolidado.pagos |
                        comisiones.consolidado.bancos')
                        <li class="nav-item">
                            <a href="{{ route('comisiones.bank') }}" class="nav-link @yield('comisiones.bank')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Consolidar Pagos </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('comisiones.import.export || comisiones.auditoria.pagos')
                        <li class="nav-item @yield('menu_reportes_comisiones')">
                            <a href="#" class="nav-link @yield('menu_reportes_comisiones_active')">
                                <i class="fas fa-file"></i>
                                <p> Reportes <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('comisiones.import.export')
                                    <li class="nav-item">
                                        <a href="{{ route('comisiones.export') }}" class="nav-link @yield('comisiones.export')">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Reporte de Comisiones </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('comisiones.auditoria.pagos')
                                    <li class="nav-item">
                                        <a href="{{ route('comisiones.incidencias.reporte') }}"
                                            class="nav-link @yield('reporte.incidencias')">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Reporte de Incidencias </p>
                                        </a>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission
                </ul>
            </li>
        @endpermission
        <!--EVALUACION DE PERSONAL -->
        @permission('evaluaciones.create|evaluaciones.edit|evaluaciones.create.masive|evaluaciones.administracion.index|evaluaciones.administracion.create')
            <li class="nav-item @yield('menu_evaluaciones')">
                <a href="#" class="nav-link @yield('evaluaciones')">
                    <i class="nav-icon fas fa-tasks"></i>
                    <p>Eval. Personal <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @permission('evaluaciones.create')
                        <li class="nav-item">
                            <a href="{{ route('evaluacion.create') }}" class="nav-link @yield('evaluacion.create')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Evaluación </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('evaluaciones.edit')
                        <li class="nav-item">
                            <a href="{{ route('evaluacion.edit') }}" class="nav-link @yield('evaluacion.edit')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Editar Evaluación </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('evaluaciones.create.masive')
                        <li class="nav-item">
                            <a href="{{ route('evaluacion.masiva.index') }}" class="nav-link @yield('evaluacion.masiva.index')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Lote de Evaluacion </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('evaluaciones.administracion.index|evaluaciones.administracion.create')
                        <li class="nav-item @yield('menu_evaluaciones_administracion')">
                            <a href="#" class="nav-link @yield('menu_evaluaciones_administracion_active')">
                                <i class="fas fa-cog"></i>
                                <p> Administracion <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('evaluaciones.administracion.index')
                                    <li class="nav-item">
                                        <a href="{{ route('evaluacion.administracion.index') }}"
                                            class="nav-link @yield('evaluaciones.administracion.index')">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Registrados </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('evaluaciones.administracion.create')
                                    <li class="nav-item">
                                        <a href="{{ route('evaluacion.administracion.create') }}"
                                            class="nav-link @yield('evaluaciones.administracion.create')">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Generar </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('evaluaciones.reportes')
                                    <li class="nav-item @yield('menu_evaluaciones_administracion_reportes')">
                                        <a href="#" class="nav-link @yield('menu_evaluaciones_administracion_reportes_active')">
                                            <i class="fas fa-file"></i>
                                            <p> Reporterias <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('evaluacion.reporte') }}" class="nav-link @yield('evaluacion.reporte')">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p> Reporte </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission
                </ul>
            </li>
        @endpermission
        <!--INVENTARIO -->
        @permission('inventario.equipos.create|inventario.equipos.index|inventario.componentes.index|inventario.componentes.create|inventario.perifericos.monitores.create|inventario.perifericos.monitores.index|
            inventario.perifericos.index|inventario.perifericos.create|inventario.perifericos.monitores.create|inventario.perifericos.monitores.index|inventario.equipos.create|inventario.equipos.index|
            inventario.posiciones.index|inventario.posiciones.create|inventario.posiciones.migration.index|inventario.administracion.cargas|inventario.administracion.etiquetas|inventario.administracion.reportes')
            <li class="nav-item @yield('menu_inventario')">
                <a href="#" class="nav-link @yield('inventario')">
                    <i class="nav-icon fas fa-cubes"></i>
                    <p>Inventario<i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @permission('inventario.componentes.index|inventario.componentes.create|inventario.perifericos.monitores.create|inventario.perifericos.monitores.index')
                        <li class="nav-item @yield('menu_stock')">
                            <a href="#" class="nav-link @yield('inventario.stock')">
                                <i class="fas fa-circle nav-icon"></i>
                                <p> Componentes <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('inventario.componentes.index')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.create.stock') }}" class="nav-link @yield('inventario.create.stock')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Incluir </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('inventario.componentes.create')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.index.stock') }}" class="nav-link @yield('inventario.index.stock')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Registros </p>
                                        </a>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    @endpermission
                    @permission('inventario.perifericos.index|inventario.perifericos.create|inventario.perifericos.monitores.create|inventario.perifericos.monitores.index')
                        <li class="nav-item @yield('menu_periferico')">
                            <a href="#" class="nav-link @yield('inventario.periferico')">
                                <i class="fas fa-circle nav-icon"></i>
                                <p> Perifericos <i class="right fas fa-angle-left"></i></p>
                            </a>
                            @permission('inventario.perifericos.index|inventario.perifericos.create')
                                <ul class="nav nav-treeview">
                                    @permission('inventario.perifericos.index')
                                        <li class="nav-item">
                                            <a href="{{ route('inventario.create.periferico') }}"
                                                class="nav-link @yield('inventario.create.periferico')">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Incluir </p>
                                            </a>
                                        </li>
                                    @endpermission
                                    @permission('inventario.perifericos.create')
                                        <li class="nav-item">
                                            <a href="{{ route('inventario.index.periferico') }}"
                                                class="nav-link @yield('inventario.index.periferico')">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Registros </p>
                                            </a>
                                        </li>
                                    @endpermission
                                </ul>
                            @endpermission
                            @permission('inventario.perifericos.monitores.create|inventario.perifericos.monitores.index')
                                <ul class="nav nav-treeview">
                                    <li class="nav-item @yield('menu_monitores')">
                                        <a href="#" class="nav-link @yield('inventario.perifericos.monitor')">
                                            <i class="fas fa-tv"></i>
                                            <p> Monitores <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @permission('inventario.perifericos.monitores.create')
                                                <li class="nav-item">
                                                    <a href="{{ route('inventario.create.monitor') }}"
                                                        class="nav-link @yield('inventario.create.monitor')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Incluir </p>
                                                    </a>
                                                </li>
                                            @endpermission
                                            @permission('inventario.perifericos.monitores.index')
                                                <li class="nav-item">
                                                    <a href="{{ route('inventario.index.monitor') }}"
                                                        class="nav-link @yield('inventario.index.monitor')">
                                                        <i class="far fa-dot-circle nav-icon"></i>
                                                        <p>Registros </p>
                                                    </a>
                                                </li>
                                            @endpermission
                                        </ul>
                                    </li>
                                </ul>
                            @endpermission
                        </li>
                    @endpermission
                </ul>
                @permission('inventario.equipos.create|inventario.equipos.index')
                    <ul class="nav nav-treeview">
                        <li class="nav-item @yield('menu_equipo')">
                            <a href="#" class="nav-link @yield('inventario.equipo')">
                                <i class="fas fa-circle nav-icon"></i>
                                <p> Equipos <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('inventario.equipos.create')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.create.equipo') }}" class="nav-link @yield('inventario.create.equipo')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Incluir </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('inventario.equipos.index')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.index.equipo') }}" class="nav-link @yield('inventario.index.equipo')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Registros </p>
                                        </a>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    </ul>
                @endpermission
                @permission('inventario.posiciones.index|inventario.posiciones.create|inventario.posiciones.migration.index')
                    <ul class="nav nav-treeview">
                        <li class="nav-item @yield('menu_posiciones')">
                            <a href="#" class="nav-link @yield('inventario.posiciones')">
                                <i class="fas fa-circle nav-icon"></i>
                                <p> Posiciones <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('inventario.posiciones.create')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.create.posiciones') }}"
                                            class="nav-link @yield('inventario.create.posiciones')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Incluir </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('inventario.posiciones.index')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.index.posiciones') }}"
                                            class="nav-link @yield('inventario.index.posiciones')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Modificar </p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('inventario.posiciones.migration.index')
                                    <li class="nav-item @yield('inventario.migraciones')">
                                        <a href="#" class="nav-link @yield('migraciones')">
                                            <i class="fas fa-tasks nav-icon"></i>
                                            <p> Migraciones <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('inventario.index.migracion') }}"
                                                    class="nav-link @yield('inventario.index.migracion')">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>Procesar </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('inventario.create.migracion') }}"
                                                    class="nav-link @yield('inventario.create.migracion')">
                                                    <i class="far fa-dot-circle nav-icon"></i>
                                                    <p>Generar </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    </ul>
                @endpermission
                @permission('inventario.administracion.cargas|inventario.administracion.etiquetas|inventario.administracion.reportes')
                    <ul class="nav nav-treeview">
                        <li class="nav-item @yield('menu_posiciones_administracion')">
                            <a href="#" class="nav-link @yield('inventario.administracion')">
                                <i class="fas fa-cog nav-icon"></i>
                                <p> Administración <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @permission('inventario.administracion.cargas')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.migracion.administracion.uploads.index') }}"
                                            class="nav-link @yield('inventario.administracion.cargas')">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Cargas M.</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('inventario.administracion.etiquetas')
                                    <li class="nav-item">
                                        <a href="{{ route('inventario.migracion.administracion.etiqueta.index') }}"
                                            class="nav-link @yield('inventario.migracion.administracion.etiqueta.index')">
                                            <i class="fas fa-qrcode nav-icon"></i>
                                            <p>Etiquetado</p>
                                        </a>
                                    </li>
                                @endpermission
                                @permission('inventario.administracion.reportes')
                                    <li class="nav-item @yield('inventario.administracion.reportes')">
                                        <a href="#" class="nav-link @yield('inventario.administracion.menu_reportes')">
                                            <i class="far fa-file-alt nav-icon"></i>
                                            <p> Reportes <i class="right fas fa-angle-left"></i></p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('inventario.migracion.administracion.reportes.index') }}"
                                                    class="nav-link @yield('inventario.administracion.reportes.generar')">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>Generar</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endpermission
                            </ul>
                        </li>
                    </ul>
                @endpermission
            </li>
        @endpermission
        <!--RECLUTAMIENTO -->
        @permission('reclutamiento.module')
            <li class="nav-item @yield('menu_reclutasimple')">
                <a href="#" class="nav-link @yield('reclutasimple')">
                    <i class="nav-icon far fa-folder-open"></i>
                    <p>Reclutamiento <i class="right fas fa-angle-left"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    @permission('reclutamiento.preseleccion.candidatos')
                        <li class="nav-item">
                            <a href="{{ route('reclutamiento.preseleccion') }}" class="nav-link @yield('reclutasimple.preselelccion')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Preselección </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('reclutamiento.estacion.listado')
                        <li class="nav-item">
                            <a href="{{ route('reclutamiento.estacion.principal') }}" class="nav-link @yield('reclutasimple.estacion.principal')">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Planilla Pre-empleo </p>
                            </a>
                        </li>
                    @endpermission
                    @permission('reclutamiento.reportes')
                        <li class="nav-item @yield('menureclutamiento.reportes')">
                            <a href="#" class="nav-link @yield('reportes')">
                                <i class="fas fa-file"></i>
                                <p> Reportes <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('reclutamiento.reportes') }}" class="nav-link @yield('reclutasimple.reportes.generar')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Generar </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endpermission
                </ul>
            </li>
        @endpermission
    </ul>
</nav>
