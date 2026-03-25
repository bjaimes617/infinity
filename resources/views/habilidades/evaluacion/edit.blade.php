@extends('layouts.main')
@section('title', 'Evaluacion de Habilidades|Listado')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_habilidades','menu-is-opening menu-open')
@section('habilidades','active')
@section('habilidades.evaluacion.editar','active')
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
                        <li class="breadcrumb-item">Evaluaciones</li>
                        <li class="breadcrumb-item">Listado</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>    
    <section class="content">
        <div class="container-fluid">         
            <div class="card">               
                <div class="card-header">
                    <h3 class="card-title">Listado de evaluaciones de habilidades blandas</h3>
                    <a href="{{route('habilidades.evaluacion.create')}}" class="btn btn-primary float-right" id="add" data-toggle="tooltip" data-placement="top" title="Añadir Evaluación"><i class="fa fa-plus-circle"></i></a>
                </div>
                <div class="card-body">
                    <p><h6> Estos resultados son extraidos de forma integra.</h6></p>                                                            
                    <table id="habilidades_table" class="table compact table-sm text-sm table-bordered table-striped text-center">     
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre Evaluación</th>
                                <th>Valor Máximo</th>
                                <th>Distribución</th>
                                <th>Rango Fecha</th>
                                <th>Estatus</th>                                        
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluaciones as $eval)
                            <tr>
                                <td>{{$eval->id}}</td>
                                <td>{{$eval->nombre_evaluacion}}</td>
                                <td>{{$eval->valor_max}} %</td>
                                <td>@if($eval->distribucion == 1) Automatica @else Manual @endif</td>
                                <td>{{date('d/m/Y',strtotime($eval->desde))}} - {{date('d/m/Y',strtotime($eval->hasta))}}</td>
                                <td>
                                    <div class="form-group">                                                                                     
                                        <input type="checkbox" id="{{$eval->id}}" class="estatusevaluacion" 
                                        name="estatusevaluacion"                                     
                                        data-backdrop="static"   
                                        data-href="{{route('eval.change.estatus', $eval->id)}}"
                                        data-keyboard="false" data-bootstrap-switch  
                                        data-off-color="danger" data-on-color="success"
                                           @if($eval->estatus == 1) checked @endif > 
                                    </div>                                   
                                </td>                                                                               
                                <td> 
                                    <form action="{{route('test.destroy', $eval->id)}}" id="destroyeeval{{$eval->id}}" method="POST" class="form-horizontal form-label-left">
                                        @method("delete")
                                        @csrf
                                        <div class="btn-group">
                                            <a href="{{route('hab.edit',$eval->id)}}" class="btn btn-primary btn-sm ">   
                                                <i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="Editar Evaluación"></i>
                                            </a>                                        
                                            <button type="submit" data-evaluacion="{{$eval->nombre_evaluacion}}"                                         
                                                    class="btn btn-danger btn-sm" data-toggle="tooltip" 
                                                    onclick="Destroy({{$eval->id}});"      
                                                     @if($eval->resultados()->count()>0) disabled @endif
                                                    data-placement="top" title="Eliminar Evaluación"
                                                     >
                                                <i class="fas fa-minus-circle"></i>
                                            </button>
                                        </div>
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
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/habilidades/plugins.js')}}"></script>
@endpush