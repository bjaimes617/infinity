@extends('layouts.main')
@section('title', 'Permisos del Sistema')
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
                        <li class="breadcrumb-item active">Permisos</li>
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
                            <h3 class="card-title">Listado de permisos del sistema</h3><a href="{{route('permissions.create')}}" class="btn btn-primary float-right" id="add" data-toggle="tooltip" data-placement="top" title="Añadir"><i class="fa fa-plus-circle"></i></a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                           <p><small>{{trans('mensajeTabla')}}</small></p>
                            <table id="permisos_table" class="table compact table-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Slug</th>
                                        <th>Descripcion</th>                                      
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permission as $permissions)
                                    <tr>
                                        <td>{{$permissions->name}}</td>
                                        <td>{{$permissions->slug}}</td>
                                        <td>{{$permissions->description}}</td>          
                                        <td>   
                                            @permission('permissions.edit | permissions.delete')
                                            <div class="btn-group">
                                                @permission('permissions.edit')
                                                <a href="{{route('permissions.edit',$permissions->id)}}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a> 
                                                @endpermission
                                                @permission('permissions.delete')
                                                <button type="button" onclick="Destroy({{$permissions->id}});" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>                        
                                                @endpermission
                                            </div>
                                            @endpermission
                                            <form  action="{{route('permissions.destroy',$permissions->id)}}" method="POST" id="eliminar{{$permissions->id}}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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
<script src="{{asset('js/permissions/permisos.js')}}" type="text/javascript"></script>
@endpush
