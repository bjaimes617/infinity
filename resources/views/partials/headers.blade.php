<!-- Left navbar links -->
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}" role="button"><i class="fas fa-home"></i></a>
    </li>
    @permission('personal.calendario.show')
        @csrf
        <li class="nav-item calendar" data-href="{{ route('personal.calendar.count') }}">
            <a class="nav-link" href="{{ route('personal.calendar.index') }}" role="button">
                <i class="fas fa-calendar"></i>
                <span class="badge badge-info navbar-badge"></span>
            </a>
        </li>
    @endpermission
    @permission('view.users|create.users|delete.users')
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><i class="fas fa-shield-alt"></i></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                @role('admin')
                    <li><a href="{{ route('activity') }}" class="dropdown-item"><i class="far fa-eye"></i> System Log</a></li>
                    <li class="dropdown-divider"></li>
                    <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="fas fa-key"></i> Permisos</a>
                        <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                            <li><a href="{{ route('permissions.index') }}" class="dropdown-item"><i class="far fa-eye"></i>
                                    Listado</a></li>
                            <li><a href="{{ route('permissions.create') }}" class="dropdown-item"><i
                                        class="fas fa-plus-circle"></i> Añadir</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="fas fa-user-tag"></i>
                            Perfiles</a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                            <li><a href="{{ route('roles.index') }}" class="dropdown-item"><i class="far fa-eye"></i>
                                    Listado</a></li>
                            <li><a href="{{ route('roles.create') }}" class="dropdown-item"><i class="fas fa-plus-circle"></i>
                                    Añadir</a></li>
                        </ul>
                    </li>
                @endrole
                @permission('view.users|create.users|delete.users')
                    <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu4" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="fas fa-users"></i>
                            Usuarios</a>
                        <ul aria-labelledby="dropdownSubMenu4" class="dropdown-menu border-0 shadow">
                            @permission('view.users')
                                <li><a href="{{ route('users.index') }}" class="dropdown-item"><i class="fas fa-users"></i>
                                        Listado</a></li>
                            @endpermission
                            @permission('create.users')
                                <li><a href="{{ route('users.create') }}" class="dropdown-item"><i class="fas fa-user-plus"></i>
                                        Añadir</a></li>
                            @endpermission
                            @permission('delete.users')
                                <li><a href="{{ route('users.remove.index') }}" class="dropdown-item"><i
                                            class="fas fa-user-minus"></i> Eliminados</a></li>
                            @endpermission
                        </ul>
                    </li>
                @endpermission
            </ul>
        </li>
    @endpermission
    @permission('estructura.index.gerencias | estructura.create.campaing | estructura.permisos.create | estructura.cecos.registrados | estructura.permisos.users')
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                class="nav-link dropdown-toggle"><i class="fas fa-sitemap"></i></a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                @permission('estructura.index.gerencias | estructura.create.campaing| estructura.cecos.registrados')
                    <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="fas fa-university"></i>
                            Gerencias</a>
                        <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                            @permission('estructura.index.gerencias')
                                <li><a href="{{ route('estructura.reporte.gerencial') }}" class="dropdown-item"><i
                                            class="far fa-file"></i> Exportar Estructura</a></li>
                            @endpermission
                            @permission('estructura.cecos.registrados')
                                <li><a href="{{ route('estructura.reporte.cecos') }}" class="dropdown-item"><i
                                            class="far fa-file"></i> Exportar Cecos</a></li>
                            @endpermission
                            @permission('estructura.index.gerencias')
                                <li><a href="{{ route('estructura.areas.index') }}" class="dropdown-item"><i
                                            class="far fa-eye"></i> Listado</a></li>
                            @endpermission
                            @permission('estructura.create.campaing')
                                <li><a href="{{ route('estructura.areas.create') }}" class="dropdown-item"><i
                                            class="fas fa-plus-circle"></i> Crear</a></li>
                            @endpermission
                        </ul>
                    </li>
                @endpermission
                <li class="dropdown-divider"></li>
                @permission('estructura.permisos.create | estructura.permisos.users')
                    <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" class="dropdown-item dropdown-toggle"><i class="fas fa-key"></i>
                            Autorización</a>
                        <ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
                            @permission('estructura.permisos.users')
                                <li><a href="{{ route('estructura.autorizacion.usuarios') }}" class="dropdown-item"><i
                                            class="far fa-eye"></i> Listado</a></li>
                            @endpermission
                            @permission('estructura.permisos.create')
                                <li><a href="{{ route('estructura.autorizacion.create') }}" class="dropdown-item"><i
                                            class="fas fa-plus-circle"></i> Crear</a></li>
                            @endpermission
                        </ul>
                    </li>
                @endpermission
            </ul>
        </li>
    @endpermission
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
    <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
        </a>
    </li>
    <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <img src="{{ Auth()->user()->RelationToPersonal != null ? \Storage::disk('personal')->url(\Auth()->user()->RelationToPersonal->image) : asset('img/user.png') }}"
                class="user-image img-circle elevation-2" alt="User Image">
            <span class="d-none d-md-inline">{{ Auth::User()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <!-- User image -->
            <li class="user-header bg-primary">
                <img src="{{ Auth()->user()->RelationToPersonal != null ? \Storage::disk('personal')->url(\Auth()->user()->RelationToPersonal->image) : asset('img/user.png') }}"
                    class="img-circle elevation-2" alt="User Image">
                <p>
                    {{ Auth::User()->name }}<br> <small>Perfil: {{ Auth::User()->roles()->first()->name }}</small>
                    <small>Registrado El: {{ date('d/m/Y', strtotime(Auth::User()->created_at)) }}</small>
                </p>
            </li>
            <!-- Menu Footer-->
            <li class="text-center user-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <div class="btn-group btn-md text-center">
                        <a href="{{ route('users.profile') }}" class="btn btn-md btn-secondary "><i
                                class="fas fa-id-badge"></i></a>
                        <a href="#" class="btn btn-md btn-secondary "><i class="far fa-question-circle"></i>
                        </a>
                        <a href="#"
                            onclick="event.preventDefault();
                                this.closest('form').submit();"
                            class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </form>
            </li>
        </ul>
    </li>
</ul>
