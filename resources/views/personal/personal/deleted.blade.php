@extends('layouts.main')
@section('title', 'Gestion de personal|Eliminados')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')

@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('personal.list','active')

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
                        <li class="breadcrumb-item">Gestión de Personal</li>
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Listado de personal eliminados</h3>
                            <p class="float-right"><a href="{{route('personal.create')}}" style="position: relative;" class="btn btn-primary " id="add" data-toggle="tooltip" data-placement="top" title="Añadir" ><i class="fa fa-plus-circle"></i></a></p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">  
                            <div class="row">
                            <div class="col-md-12" id="tablelistado"> 
                                <br/>
                                <h6>  Estos resultados son extraidos de forma integra del Listado de Personal de Directa Group</h6>    
                                 <div class="ln_solid"></div>
                                <table id="personal_deleted"  class="table compact table-sm text-sm table-bordered table-striped text-center">
                                    <thead>
                                        <tr>                                            
                                            <th>Cedula</th>
                                            <th>Nombre y Apellido</th>
                                            <th>Fecha de Ingreso</th>
                                            <th>Gerencia</th>                                    
                                            <th>Campaña</th> 
                                            <th>Skill</th> 
                                            <th>Cargo</th>   
                                            <th>Situ. Contractual</th>
                                            <th>Estatus</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $personal->load('RelationCargos','RelationEstatus','RelationAreas','RelationCartera') @endphp
                                        @foreach($personal as $personals)
                                        <tr>                                           
                                            <td>{{$personals->cedula_empleado}}</td>
                                            <td>{{$personals->primer_nombre}} {{$personals->primer_apellido}}</td>
                                            <td>{{date('Y/m/d',strtotime($personals->fecha_ingreso))}}</td>
                                            <td>{{$personals->RelationAreas->nombre_campana}}</td>
                                            <td>{{$personals->RelationCartera->nombre_cartera}}</td>
                                            @if($personals->RelationCargos != null)
                                            <td>{{$personals->RelationCargos->nombre_cargo}}</td>
                                            @else
                                            <td> - </td>
                                            @endif
                                            <td>{{$personals->situacion_contractual}}</td>
                                            <td>{{$personals->RelationEstatus->estatus}}</td>
                                            <td>{{date('d/m/Y',strtotime($personals->deleted_at))}}</td>
                                            <td>  
                                                @permission('personal.restore || personal.delete')                                                                                      
                                                <form action="{{route('personal.destroy',$personals->id)}}" method="POST" id="delete_personal{{$personals->id}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="btn-group">
                                                        @permission('personal.restore')
                                                        <a href="{{route('personal.restore',$personals->id)}}" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="left" title="Restaurar Expediente"><i class="fas fa-recycle"></i></a>
                                                        @endpermission 
                                                        
                                                        @permission('personal.delete')
                                                        <a href="#" class="btn btn-sm btn-danger" onclick="if(!confirm('¿Desea eliminar el Expediente en su totalidad?, Esta Acción no se puede reversar.'))
                                                        { return false; } else {$('#delete_personal'+'{!!$personals->id!!}').submit(); }" data-toggle="tooltip" data-placement="left" title="Eliminar Expediente"><i class="fas fa-user-times"></i></a>
                                                         @endpermission
                                                    </div>
                                                </form>                                               
                                                @endpermission
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
<script src="{{asset('js/gestion_personal/gestion.js')}}" type="text/javascript"></script>
@endpush
