@extends('layouts.main')
@section('title', 'Roles del Sistema')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Seguridad</li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Roles Generales para los usuarios</h3> 
                            <div class="btn-group  float-right">
                                <a href="{{route('roles.create')}}" class="btn btn-primary" id="add" data-toggle="tooltip" data-placement="top" title="Añadir">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                                <a href="{{route('roles.remove.index')}}" class="btn btn-danger" id="deleted" data-toggle="tooltip" data-placement="top" title="Ver Eliminados">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <p><small>{{trans('mensajeTabla')}}</small></p>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <table id="roles_table" class="table compact table-sm table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Slug</th>
                                                <th>Descripcion</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $role)
                                            <tr>
                                                <td>{{$role->name}}</td>
                                                <td>{{$role->slug}}</td>
                                                <td>{{$role->description}}</td>
                                                <td> 
                                                    @permission('roles.edit | roles.delete')
                                                    <div class="btn-group">
                                                        @permission('roles.edit')
                                                        <a href="{{route('roles.edit',$role->id)}}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a> 
                                                        @endpermission
                                                        @permission('roles.delete')
                                                        <button type="button" onclick="Destroy({{$role->id}});" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>                        
                                                        @endpermission
                                                    </div>
                                                    @endpermission
                                                    <form  action="{{route('roles.remove',$role->id)}}" method="POST" id="eliminar{{$role->id}}">
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
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/roles/roles.js')}}" type="text/javascript"></script>
@endpush