@extends('layouts.main')
@section('title', 'Habilidades|Reportes')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')

@section('menu_habilidades','menu-is-opening menu-open')
@section('menu_habilidades_eval','menu-is-opening menu-open')
@section('habilidades','active')
@section('evaluaciones.habilidades.reportes','active')
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
                        <li class="breadcrumb-item">Reportes</li>
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
                                Generar Reporte de Habilidades Blandas
                            </h3>
                        </div>    
                        <form method="post" id="reportes_create" action="{{route('habilidades.reportes.generar')}}" class="form-horizontal form-label-left">
                            @csrf
                            <div class="card-body">    
                                <div class="row mb-3">
                                    <div class="col-sm-12">
                                        <label for="evaluacion" class="col-form-label">Evaluación</label> <span class="required" style="color:red;"> * </span>
                                        <div class="form-group">                                       
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-file-signature"></i></span>
                                                </div>
                                                <select name="evaluacion" id="evaluacion" data-href="{{route('search.personal.cargo')}}" class="select2 form-control" required>
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($evaluaciones as $evaluacion)
                                                    <option value="{{$evaluacion->id}}">{{$evaluacion->nombre_evaluacion}}</option>
                                                    @endforeach
                                                </select>  
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="gerencia">Gerencia <span style="color:red">*</span></label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select name="gerencia[]" id="gerencia" required
                                                        data-href="{{route('selector.areas.estructuras')}}" multiple="multiple" class="form-control select2">                                                                                                              
                                                    @foreach($gerencias as $gerencia)
                                                    <option value='{{$gerencia->id}}'>{{$gerencia->nombre_campana}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="campanas">Unidad/Campaña <span style="color:red">*</span></label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select name="campanas[]" id="campanas" required  multiple="multiple"
                                                        data-href="{{route('selector.carteras.estructuras')}}" class="form-control select2">
                                                    <option value=''> [SELECCIONE GERENCIA]</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select name="skill[]" id="skill" required multiple="multiple"                                                                                                                 
                                                        class="form-control select2"  style="width: 100%;" data-href="{{route('search.personal.cargo')}}">
                                                    <option value=''> [SELECCIONE GERENCIA]</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="personal_cargo" class="col-form-label">Personal Evaluado</label> <span class="required" style="color:red;"> * </span>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div>
                                                <select name="personal_cargo" id="personal_cargo" class="select2 form-control" required data-href="{{route('cargo.historico')}}">
                                                    <option value="">[SELECCIONE]</option>
                                                </select>  
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="personal_cargo" class="col-form-label">Cargo</label> <span class="required" style="color:red;"> * </span>
                                        <div class="form-group">                                       
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                </div>
                                                <select name="position_name" id="position_name" class="select2 form-control" required>
                                                    <option value="">[SELECCIONE]</option>
                                                </select>  
                                            </div> 
                                        </div>
                                    </div>
                                    <input type="hidden" name="sede" id="sede">
                                    <input type="hidden" name="rango" id="rango">
                                    <div class="col-sm-6">                                    
                                        <label for="rango_eval" class="col-form-label">Rango Fecha Evaluación</label> <span class="required" style="color:red;"> * </span>           
                                        <div class="form-group">                                     
                                            <div class="input-group">     
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>                                   
                                                <input type="text" name="rango_eval" class="form-control daterange" id="rango_eval" placeholder="[FECHA]" required data-parsley-required-message="Este campo es obligatorio">
                                            </div>  
                                        </div>
                                    </div>
                                    <!--<div class="col-sm-4">                                    
                                        <div class="form-group">
                                            <label for="tipo_reporte" class="col-form-label">Tipo de Reporte</label> <span class="required" style="color:red;"> * </span>           
                                            <div class="input-group">     
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-file"></i></span>
                                                </div>                                   
                                                <select name="tipo_reporte" id="tipo_reporte" class="required form-control" required>
                                                    <option value="">[SELECCIONE]</option>
                                                    <option value="1">Respuestas de Formulario</option>
                                                    <option value="2">RF</option>
                                                    <option value="3">Informe</option>
                                                    <option value="4">Tabulador</option>
                                                </select>  
                                            </div>  
                                        </div>
                                    </div>-->                    

                                    <div class="col-md-12">
                                        <div class="btn-group btn-block">
                                            <button class="btn btn-primary " type="submit"><i class="fa fa-download"></i> Generar Reporte</button>
                                            <button class="btn btn-success " type="button"                                                     
                                            id="generarGrafico" data-href="{{route('generar.grafico')}}"><i class="fa fa-chart-pie"></i> Ver Estadisticas</button>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                        </form>                        
                        <div id="modal_grafico" class="modal fade" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="grafico-content">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td><b>Nombre y Apellido del Evaluado:</b></td>
                                                    <td><span id="nombre"></span></td>
                                                    <td><b>Sede:</b></td>
                                                    <td><span id="sede_m"></span></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Cargo del Evaluado:</b></td>
                                                    <td><span id="cargo"></span></td>
                                                    <td><b>Fecha de Evaluación:</b></td>
                                                    <td><span id="fecha"></span></td>
                                                </tr>
                                            </table>
                                        </div><br>
                                        <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 70vh;">
                                            <canvas id="habilidad_grafico" style="display: none;"></canvas>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="descargarGrafico" type="button" class="btn btn-primary"><i class="fa fa-download"></i>Descargar Gráfico</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="{{asset('js/habilidades/reportes.js')}}"></script>
@endpush