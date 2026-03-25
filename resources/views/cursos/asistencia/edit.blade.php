@extends('layouts.main')
@section('title', 'Cursos|Asistencia|Editar')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('cursosactive','active')
@section('cursos.asistencia.create','active')
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
                        <li class="breadcrumb-item active">Modificar</li>
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
                    <h3 class="card-title">Asistencia del Participante <small>Los Campos marcados con <span style="color:red;">*</span> son obligatorios</small></h3>                            
                    <p class="float-right">                                
                    </p>
                </div>
                <form action="{{route('cursos.asistencia.update',$asistencia->id)}}" id="editasistencia" method="POST" class='form-horizontal'>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                @method('PUT')
                                @CSRF
                                <div class="row mb-3">   
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="facilitador">Facilitador Encargado <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <input type="text" name="facilitador" id="facilitador" disabled value='{{$asistencia->RelationPersonal->primer_nombre}} {{$asistencia->RelationPersonal->primer_apellido}}' autocomplete="off" required class="form-control">                                              
                                            </div> 
                                        </div>      
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="curso">Curso Asignado <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <input type="text" name="curso" id="curso" disabled value='{{$asistencia->RelationCursos->Relationskill->nombre_skill}}' autocomplete="off" required class="form-control">                                              
                                            </div> 
                                        </div>      
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="cursoasistencia">Asistencia registrada <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <select id="cursoasistencia" name="cursoasistencia" data-valor="{{$asistencia->id}}" required class="select2 inputasistencia" style="width: 100%;">
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($itemsasistencia as $key => $curs)
                                                    <option value="{{$key}}" @if($asistencia->prefijo == $key) Selected @endif>{{$curs}}</option>
                                                    @endforeach
                                                </select>                                                   
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="observaciones">Observaciones <span style="color:red">*</span></label>  
                                            <div class="input-group">                                              
                                                <input type="text" name="observaciones" id="observaciones" value='{{$asistencia->observacion}}' autocomplete="off" required class="form-control">                                              
                                            </div> 
                                        </div>      
                                    </div>                                
                                    @if(in_array($asistencia->prefijo,["E","A"]))
                                    <div class="col-md-12 motivos" id="display{{$asistencia->id}}">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="motivo{{$asistencia->id}}">Motivo del Egreso<span style="color:red">*</span></label>  
                                            <div class="input-group">    
                                                <select id="motivo{{$asistencia->id}}" name="motivo{{$asistencia->id}}" required class="select2" style="width: 100%;">
                                                    <option value="">[Seleccione]</option>                                
                                                    <option value="{{$asistencia->RelationParticipantes->relationObservacionesCurso->id}}" selected>{{$asistencia->RelationParticipantes->relationObservacionesCurso->observaciones}}</option>                              
                                                </select>                            
                                            </div> 
                                        </div>   
                                    </div>
                                    @endif
                                </div>                                

                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-lg btn-primary"  data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
                    </div>
                </form>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<script>
    var egresos = {!!$egresos!!}
    ;
    var abandonos = {!!$abandonos!!}
    ;
</script>
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/cursos/plugins.js')}}"></script>
@endpush
