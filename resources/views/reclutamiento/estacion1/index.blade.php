@extends('layouts.main')
@section('title', 'Estación Principal')
@section('menu_reclutasimple','menu-is-opening menu-open')
@section('reclutasimple','active')
@section('reclutasimple.estacion.principal','active')
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
                        <li class="breadcrumb-item">Reclutamiento</li>
                        <li class="breadcrumb-item active">Estacion 1</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>     
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Buscador y Listado de Prospectos</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12" >                                                           
                            <p><h6> Estos Datos extraidos integro desde la Base de datos.</h6></p>                               
                            <table id="datatable-prospectos" class="table compact table-sm text-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>        
                                         <th>Campaña</th>
                                        <th>Cedula</th>
                                        <th>Nombre y Apellido</th>   
                                        <th>Edad</th>    
                                        <th>Fotografia</th>    
                                        <th>Red Social</th>
                                        <th>Registrado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prospectos->chunk(50) as $chunk)
                                        @foreach($chunk as $post)
                                        <tr id="candidato{{$post->id}}">
                                            <td>{{$post->campania}}</td>
                                            <td>{{$post->cedula}}</td>
                                            <td>{{$post->nombres}} {{$post->apellidos}}</td>
                                            <td>{{$post->edad}}</td>
                                            <th> @if($post->photo != null) <i class="fas fa-check-circle"></i> @else <i class="fas fa-times-circle"></i> @endif </th> 
                                            <td>{{$post->redes}}</td>
                                            <td>{{date('d/m/Y',strtotime($post->created_at))}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-primary btn-md" id="{{$post->id}}"
                                                            data-toggle="modal" 
                                                            onclick="ShowPlanillaProspecto({{$post->id}});" 
                                                            data-href="{{route('reclutamiento.estacion.showProspecto',$post->id)}}"
                                                            data-puthref="{{route('reclutamiento.estacion.updateProspecto',$post->id)}}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a class="btn btn-info" href="{{route('reclutamiento.estacion.pdfProspecto',$post->id)}}" target="_blank">
                                                        <i class="far fa-file-pdf"></i>
                                                    </a>
                                                    <button class="btn btn-success btn-md" id="ButtonCurso{{$post->id}}" 
                                                            data-aprobacion="{{route('reclutamiento.estacion.paseaCurso',$post->id)}}"
                                                            data-toggle="modal" onclick="ShowCursoAvailable({{$post->id}});" >
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>                                
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="ModalAceptCurso">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Seleccion de Participanhtes</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="POST" id="AprobadoaACurso" class="form-horizontal form-label-left" files="true" enctype='multipart/form-data' >
                        @csrf                                                   
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label" for="referido">Referidos Por <span style="color:red">*</span></label>                                                                                                                                      
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <select class="form-control" id="referido" name="referido" required="required">
                                                <option value="">[SELECCIONE]</option>
                                                <option value="1">Reclutamiento</option>
                                                <option value="2">Operaciones</option>
                                            </select>
                                        </div>
                                    </div> 
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label" for="curso">Asignar Participantes al curso<span style="color:red">*</span></label>                                                                                                                                                                              
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            </div>
                                            <select class="form-control" id="curso" name="curso" required="required">
                                                <option value="">[SELECCIONE]</option>
                                                @foreach($cursos as $curs)
                                                <option value="{{$curs->id}}">DGC{{$curs->id}} | {{date('d/m/Y',strtotime($curs->inicio))}} al {{date('d/m/Y',strtotime($curs->finaliza))}} | {{$curs->Relationskill->nombre_skill}} | Fac:{{$curs->RelationPersonal->primer_nombre}} {{$curs->RelationPersonal->primer_apellido}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div> 
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label" for="horario">Horario Operativo<span style="color:red">*</span></label>                                                                                                                                                                              
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            </div>
                                            <select class="form-control" id="horario" name="horario" required="required">
                                                <option value="">[SELECCIONE]</option>
                                                @foreach(config('app.horariolaboral') as $hor => $curs)
                                                <option value="{{$hor}}">{{$curs}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div> 
                                </div>
                                <div class="col-md-12 text-center">
                                    <p><b>ATENCION:</b> Antes de Enviar el canditado al curso, asegurese de haber exportado la planilla de Solicitud de empleo.</p>
                                </div>
                            </div>                        
                        </div>
                        <input type="hidden" name="canditadorow" id="candidatorow">
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Aprobar</button>
                        </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            
            <div class="modal fade" id="modal-xl">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Visualización y Actualización de Datos</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="POST" id="updateinfoparticipante" class="form-horizontal form-label-left" files="true" enctype='multipart/form-data' >
                        @csrf   
                        @method('PUT')
                        <div class="modal-body" id="appenModaldinputs">
                           
                        </div>                        
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Actualizar Planilla</button>
                        </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>     
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker//src/js/bootstrap-datetimepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/jquery-validation/jquery.validate.js')}}" type="text/javascript"></script>   
<script src="{{asset('plugins/jquery-validation/localization/messages_'.env('APP_LOCALE').'.js')}}"></script>    
<script src="{{asset('js/reclutamiento/plugins.js')}}" type="text/javascript"></script>

@endpush
