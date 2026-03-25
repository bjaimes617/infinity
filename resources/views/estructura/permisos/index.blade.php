@extends('layouts.main')
@section('title', 'Estructura|Autorización')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Estructura</li>
                            <li class="breadcrumb-item ">Autorización</li>
                            <li class="breadcrumb-item active">Registradas</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Usuarios del sistema con Autorizaci&oacute;n sobre Unidades/Campa&ntilde;as
                        </h3> @permission('estructura.permisos.create')
                            <a href="{{ route('estructura.autorizacion.create') }}" class="btn btn-primary float-right"
                                id="add" data-toggle="tooltip" data-placement="left" title="Nueva Autorización"><i
                                    class="fa fa-plus-circle"></i></a>
                        @endpermission
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <input type="hidden" id="searchPermissionsUsers"
                                    value="{{ route('estructura.usuarios.autorizados') }}">
                                @csrf
                                <p><small>{{ trans('mensajeTabla') }}</small></p>
                                <table id="permisos_table"
                                    class="table compact table-sm table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Cedula</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user as $users)
                                            <tr>
                                                <td>{!! $users->RelationUsers != null
                                                    ? ($users->RelationUsers->deleted_at != null
                                                        ? '<span class="text-danger">' . $users->RelationUsers->name . '</span>'
                                                        : $users->RelationUsers->name)
                                                    : 'N/D' !!}
                                                </td>
                                                <td>{!! $users->RelationUsers != null
                                                    ? ($users->RelationUsers->deleted_at != null
                                                        ? '<span class="text-danger">' . $users->RelationUsers->cedula . '</span>'
                                                        : $users->RelationUsers->cedula)
                                                    : 'N/D' !!}
                                                </td>
                                                <td>{!! $users->RelationUsers != null
                                                    ? ($users->RelationUsers->deleted_at != null
                                                        ? '<span class="text-danger">' . $users->RelationUsers->username . '</span>'
                                                        : $users->RelationUsers->username)
                                                    : 'N/D' !!}
                                                </td>
                                                <td>{!! $users->RelationUsers != null
                                                    ? ($users->RelationUsers->deleted_at != null
                                                        ? '<span class="text-danger">' . $users->RelationUsers->email . '</span>'
                                                        : $users->RelationUsers->email)
                                                    : 'N/D' !!}
                                                </td>
                                                <td>
                                                    <div class='btn-group'>
                                                        @permission('estructura.permisos.create')
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="ModalPermissions({{ $users->user_id }});"><i
                                                                    class="fas fa-eye"></i></button>
                                                            <a class="btn btn-primary btn-sm"
                                                                href="{{ route('estructura.autorizacion.edit', $users->user_id) }}"><i
                                                                    class="fas fa-edit"></i></a>
                                                            <button type="button" onclick="Destroy({{ $users->user_id }});"
                                                                class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endpermission
                                                    </div>
                                                    <form
                                                        action="{{ route('estructura.autorizacion.delete', $users->user_id) }}"
                                                        method="POST" id="eliminar{{ $users->user_id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="modal fade" id="modal-permisos">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Autorización del skill Otorgadas al Usuario</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4> Autorizaciones del usuario. <b><span id="RolPrincipal"
                                                    style="color:red;"></span></b></h4>
                                    </div>
                                    <br>
                                    <hr>
                                    <div class="col-md-12">
                                        <table id="PermissionsUsers" class="table table-bordered table-striped text-center">
                                            <thead>
                                                <tr>
                                                    <th>Gerencia</th>
                                                    <th>Unidad/Campaña</th>
                                                    <th>Segmento/Skill</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
    </div>
@endsection
@push('styles')
    <link media="all" type="text/css" rel="stylesheet"
        href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet"
        href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet"
        href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/estructura/autorizacion.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
@endpush
