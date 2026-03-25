@extends('layouts.main')
@section('title', 'Cursos|Asignacion Participantes')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('cursosactive','active')
@section('menu.cursos.administracion','menu-is-opening menu-open')
@section('menu.cursos.administracion.active','active')
@section('cursos.asignacion','active')
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
                        <li class="breadcrumb-item">Cursos</li>
                        <li class="breadcrumb-item active">Asginación</li>
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
                            <h3 class="card-title">Asignación de participantes </h3>                            
                            <p class="float-right">                                
                            </p>
                        </div>
                        <div class="card-body">                           
                            <form method="POST" action="{{route('cursos.asignacion.store')}}" id="create-participantes"  class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                           <label class="col-form-label" for="curso">Referidos Por  <span class="required" style="color:red;"> * </span>  </label>            
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <select class="form-control" id="referido" name="referido" required="required">
                                                <option value="">[SELECCIONE]</option>    
                                                @foreach($referidos as $key => $convocado)
                                                <option value="{{$key}}">{{$convocado}}</option>   
                                                @endforeach
                                            </select>
                                        </div> 

                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">                                        
                                        <label class="col-form-label" for="curso">Asignar Participantes al curso  <span class="required" style="color:red;"> * </span>  </label>            
                                        <div class="input-group">                                            
                                            <select class="form-control" id="curso" name="curso" required="required">
                                                <option value="">[SELECCIONE]</option>
                                                @foreach($cursos as $curs)
                                                <option value="{{$curs->id}}">DGC{{$curs->id}} | {{date('d/m/Y',strtotime($curs->inicio))}} al {{date('d/m/Y',strtotime($curs->finaliza))}} | {{$curs->Relationskill->nombre_skill}} | Fac:{{$curs->RelationPersonal->primer_nombre}} {{$curs->RelationPersonal->primer_apellido}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="col-form-label" for="archivo">Buscar Archivo de Participantes  <span class="required" style="color:red;"> * </span>  </label>                                        
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                                <label class="custom-file-label" for="archivo">Buscar Archivo en...</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary "><i class="fa fa-upload"></i> Cargar</button>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-12">
                                    <br />                                  
                                    <h5 Style="color: red;"> Recomendaciones a considerar al momento de subir el archivo:</h5>
                                    <p><h6>Tenga en cuenta, que podra asignar participantes a los cursos que aun no esten aperturados y hasta 2 día despues de iniciar.</h6></p>
                                    <p><h6>Utilice el formato suministrado para cargar los datos basicos de  los participantes, si no lo tienes Descarga <a href="{{ \Storage::disk('public')->url('Formato Participantes Cursos.xlsx')}}">Aqui</a></h6></p>
                                    <p><h6>No realice ninguna modificación en las cabeceras de las columnas.</h6></p> 
                                </div> 
                            </div>
                            </form                            
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>

@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->

<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush