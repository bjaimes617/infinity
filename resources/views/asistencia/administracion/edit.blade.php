@extends('layouts.main')
@section('title', 'Asistencia Operativa|Modificacion|Editar')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_asistencia','menu-is-opening menu-open')
@section('asistencia','active')
@section('asistencia.view','active')

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
                        <li class="breadcrumb-item">Asistencia Operativa</li>
                        <li class="breadcrumb-item active">Modificaciones</li>
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
                    <h3 class="card-title">Modificación de Asistencia del Día: {{trans('semanas.'.$asistencia->dia)}} {{date('d/m/Y',strtotime($asistencia->created_at))}}</h3>
                </div>
                <form method="POST" action="{{route('asistencia.administracion.update',$asistencia->id)}}" id="asistencias_edit"  class="form-horizontal form-label-left">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p> <b>Nombre Y Apellido:</b> {{$asistencia->RelationPersonal->primer_nombre.' '.$asistencia->RelationPersonal->primer_apellido}}
                                <br> <b>Supervisor Inmeadiato:</b> {{$asistencia->RelationListAsistenciaJefeInmediato->primer_nombre}} {{$asistencia->RelationListAsistenciaJefeInmediato->primer_apellido}}
                                <br> <b>Coordinador/Senior Inmeadiato:</b> {{$asistencia->RelationListAsistenciaJefeInmediatosegundo->primer_nombre}} {{$asistencia->RelationListAsistenciaJefeInmediatosegundo->primer_apellido}}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="listado">Prefijos de La asistencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="listado" id="listado" required class="form-control" width="100%">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($items as $key => $item)
                                            <option value="{{$key}}" @if($asistencia->prefijo == $key) selected @endif >{{$item}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="observaciones">Observaciones de asistencia <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <input type="text" id="observaciones" value="{{$asistencia->observacion}}" name="observaciones" required class="form-control">                                                                                               
                                    </div> 
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-lg btn-block btn-primary"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/asistencia/plugins.js')}}" type="text/javascript"></script>
@endpush