@extends('layouts.main')
@section('title', 'Roles del Sistema|Eliminados')
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
                        <li class="breadcrumb-item ">Roles</li>
                        <li class="breadcrumb-item active">Eliminados</li>
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
                            <h3 class="card-title">Roles de usuarios Removidos</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">                            
                            <table id="roles_table" class="table table-sm compact table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>slug</th>
                                        <th>Descripcion</th>                                       
                                        <th>Creado</th>                                      
                                        <th>Removido</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{$role->name}}</td>
                                        <td>{{$role->slug}}</td>
                                        <td>{{$role->description}}</td>                                       
                                        <td>{{date('d/m/Y',strtotime($role->created_at))}}</td>      
                                        <td>{{date('d/m/Y',strtotime($role->deleted_at))}}</td>          
                                        <td> 
                                            @permission('roles.restore | roles.delete')
                                            <div class="btn-group">
                                                @permission('roles.restore')
                                                <form  action="{{route('roles.restore',$role->id)}}" method="POST">
                                                    <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-recycle"></i></button>                        
                                                    @csrf
                                                    @method('PUT')
                                                </form>                                                        
                                                @endpermission
                                                @permission('roles.delete')
                                                <button type="button" onclick="Destroy({{$role->id}});" class="btn btn-danger btn-sm"><i class="fas fa-minus-circle"></i></button>                        
                                                @endpermission
                                            </div>
                                            @endpermission
                                            <form  action="{{route('roles.destroy',$role->id)}}" method="POST" id="eliminar{{$role->id}}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>                                       
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
