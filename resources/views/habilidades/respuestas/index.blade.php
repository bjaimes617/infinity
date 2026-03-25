@extends('layouts.main')
@section('title', 'Habilidades|Respuestas|Añadir')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_habilidades','menu-is-opening menu-open')
@section('menu_habilidades_eval','menu-is-opening menu-open')
@section('habilidades','active')
@section('evaluaciones.habilidades.respuestas','active')
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
                        <li class="breadcrumb-item">Habilidades</li>
                        <li class="breadcrumb-item">Respuestas</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-tasks"></i>
                                Generar Respuestas
                            </h3>
                        </div> 
                        @csrf
                        <div class="card-body">    
                            <div class="row mb-3">
                                <div class="col-md-6">                                    
                                    <label for="evaluacion" class="col-form-label">Evaluacion <span class="required" style="color:red;"> * </span></label>            
                                    <div class="form-group">                                       
                                        <div class="input-group">                                        
                                            <select id="evaluacion" name="evaluacion" class="form-control select2" data-href="{{route('search.categoria.respuestas')}}" style="width: 100%;" required>
                                                <option value="">[SELECCIONE]</option>
                                                @foreach($evaluaciones as $eval)
                                                <option value="{{$eval->id}}">{{$eval->nombre_evaluacion}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="categorias" class="col-form-label">Categorias <span style="color:red"> *</span></label>
                                    <div class="form-group">                                        
                                        <div class="input-group"> 
                                            <select id="categorias" name="categorias" class="form-control select2" data-href="{{route('consult.pregunta')}}" style="width: 100%;" required>

                                            </select>
                                        </div>
                                    </div>
                                </div>                                                                                           
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12" id="tablelistado" style="display:none;">
                                    <hr>
                                    <p><h6> Estos resultados son extraidos de forma integra.</h6></p>                                                            
                                    <table id="preguntas_table" class="table compact table-sm text-sm table-bordered table-striped text-center">     
                                        <thead>
                                            <tr>                                                   
                                                <th>Cod pregunta</th>
                                                <th>Preguntas</th>                                                                                     
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid-->
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
<script src="{{asset('js/habilidades/respuestas.js')}}"></script>
@endpush