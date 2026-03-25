@extends('layouts.main')
@section('title', 'Usuarios del Sistema|Eliminados')
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
                        <li class="breadcrumb-item ">Usuarios</li>
                        <li class="breadcrumb-item active">Eliminados</li>
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
                    <h3 class="card-title">Listado de usuarios eliminados</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p><small>{{trans('mensajeTabla')}}</small></p>                        
                            <table id="usuarios_table" class="table table-sm compact table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Nombre y Apellido</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Estatus</th>
                                        <th>Creado</th>                                      
                                        <th>Removido</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->username}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->estatus_id}}</td>
                                        <td>{{date('d/m/Y',strtotime($user->created_at))}}</td>      
                                        <td>{{date('d/m/Y',strtotime($user->deleted_at))}}</td>          
                                        <td> 
                                            @permission('restore.users | delete.users')
                                            <div class="btn-group">
                                                @permission('restore.users')
                                                <form  action="{{route('users.restore',$user->id)}}" method="POST">
                                                    <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-recycle"></i></button>                        
                                                    @csrf
                                                    @method('PUT')
                                                </form>                                                        
                                                @endpermission
                                                @permission('delete.users')
                                                <button type="button" onclick="Destroy({{$user->id}});" class="btn btn-danger btn-sm"><i class="fas fa-minus-circle"></i></button>                        
                                                @endpermission
                                            </div>
                                            @endpermission
                                            <form  action="{{route('users.destroy',$user->id)}}" method="POST" id="eliminar{{$user->id}}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td> 
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <!-- /.card-body -->
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
<script src="{{asset('js/user/users.js')}}" type="text/javascript"></script>
@endpush
