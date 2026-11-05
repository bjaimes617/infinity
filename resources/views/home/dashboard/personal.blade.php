@extends('layouts.main')
@section('title', 'Dashboard Personal')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">

                    </ol>
                </div>
            </div>       
            <!--CONTENCT PRINCIPAL-->
            <div class="row">
                <div class="col-3 col-sm-3 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary elevation-0"><i class="fas fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Mes y Año</span>
                            <span class="info-box-number">
                                <div class="form-group">
                                    <div class='input-group' id='fechar'>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type='text' class="form-control" autocomplete="off" data-href="{{ route('dashboard.personal.update') }}" value="{{ date('m/Y') }}" required name="mes" id="mes"/>
                                    </div>
                                </div>
                            </span>
                        </div>                
                    </div>        
                </div> 
                <div class="col-9 col-sm-9 col-md-9">
                    <form action="{{route('update.dashboard.required')}}" method="POST" id="update-dashboard-required" class="form-horizontal form-label-left" enctype='multipart/form-data'>                            
                        @csrf
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-0"><i class="fas fa-upload"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Requeridos del Mes | <small><b>Descargue el Formato de Carga de los Requeridos <a href="{{\Storage::disk('public')->url('formato_carga_requeridos.xlsx') }}" class="text-danger" target="_blank">(aqui)</a></small></b></span>
                                <span class="info-box-number">
                                    <div class="form-group">                                   
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                                <label class="custom-file-label" for="exampleInputFile">Buscar Archivo en...</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button type="submit"  class="btn btn-primary cargamasiva"><i class="fa fa-upload"></i></button>
                                            </div>
                                        </div>
                                    </div>  
                                </span>
                            </div>                
                        </div>
                    </form>     
                </div>                  
                <div class="col-4 col-sm-4 col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Personal Requerido</span>
                            <span class="info-box-number" id="total-requeridos">
                                {{ $totalrequeridos}}
                                <small>Requeridos</small>
                            </span>
                        </div>                
                    </div>        
                </div> 
                <div class="col-4 col-sm-4 col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-plus"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Personal Activo</span>
                            <span class="info-box-number" id="total-activos">
                                {{ $totalactivos}}
                                <small>Activos</small>
                            </span>
                        </div>                
                    </div>        
                </div> 
                <div class="col-4 col-sm-4 col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-people-arrows"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Diferencial</span>
                            <span class="info-box-number" id="total-diferencial">
                                {!! $totaldiferencial!!}
                                <small>Diferencia</small>
                            </span>
                        </div>                
                    </div>        
                </div>                    
                <!--BARCHAT-->          
                <section class="col-lg-12 connectedSortable">       
                    <!--apartado apra los grafica-->
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i>
                                Personal Activo Vs Requerido                        
                            </h3>
                            <div class="card-tools">  
                                <button type="button" class="btn btn-info btn-sm" 
                                        data-toggle="modal" data-target="#modal-grafico-personal"
                                        data-toggle="tooltip" data-placement="top" title="Detalles Por Campaña">
                                    <i class="fa fa-table"></i> 
                                </button>       

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>                  
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div> 
                </section>  
                <!--PIECHART-->          
                <section class="col-lg-8 connectedSortable">       
                    <!--apartado apra los grafica-->
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i>
                                Motivos de Egresos Global                       
                            </h3>
                            <div class="card-tools">   

                                <button type="button" class="btn btn-info btn-sm" 
                                        data-toggle="modal" data-target="#modal-motivos-egresos"
                                        data-toggle="tooltip" data-placement="top" title="Detalles Por Campaña">
                                    <i class="fa fa-table"></i> 
                                </button>  

                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>                  
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="pieChartAbsentismo" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div> 
                </section>
                <!--INFOABSTENCION-->          
                <section class="col-lg-4 connectedSortable">       
                    <!--apartado apra los grafica-->
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i>
                                Absentismo y Rotación General                       
                            </h3>
                            <div class="card-tools"> 
                                 <button type="button" class="btn btn-info btn-sm" 
                                        data-toggle="modal" data-target="#modal-indicadores"
                                        data-toggle="tooltip" data-placement="top" title="Indicadores por Campaña">
                                    <i class="fa fa-table"></i> 
                                </button>  
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>                  
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-2 bg-danger">
                                <span class="info-box-icon"><i class="fas fa-sync"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Porcentaje de Rotacion </span>
                                    <span class="info-box-number"><h4>{{round($rotacionIndiceAbsGeneral["rotacion"],2)}} %</h4></span>
                                </div>             
                            </div>
                            <div class="info-box mb-2 bg-warning">
                                <span class="info-box-icon"><i class="fas fa-hand-point-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Indice de Rotación</span>
                                    <span class="info-box-number"><h4>{{round($rotacionIndiceAbsGeneral["indice"],2)}} %</h4></span>
                                </div>              
                            </div>
                            <div class="info-box mb-2 bg-info">
                                <span class="info-box-icon"><i class="fas fa-hands"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text">Absentismo</span>
                                    <span class="info-box-number"><h4>{{round($rotacionIndiceAbsGeneral["absentismo"],2)}} %</h4></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                    </div> 
                </section>             
            </div>

            <!--MODAL INFORMATIVO PERSONAL REQUERIDO VS ACTIVOS-->
            <div class="modal fade" id="modal-grafico-personal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-users"></i> Detalles Por Campaña del Personal Activo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm compact text-sm table-bordered table-striped text-center" id="datatable-skills"> 
                                <thead style="background-color:#BE3A8D; color:white; font-weight:bold; text-align:left;">
                                    <tr>
                                        <th>#</th>
                                        <th>Unidad</th>
                                        <th>Requerido</th>
                                        <th>Activos</th>
                                        <th>Diferencial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datos as $dato)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="{{'customCheckbox'.$dato["cartera"]}}" value="{{$dato["cartera"]}}">
                                                <label for="{{'customCheckbox'.$dato["cartera"]}}" class="custom-control-label"></label>
                                            </div>
                                        </td>
                                        <td>{{$dato["cartera"]}}</td>
                                        <td>{{$dato["requerido"]}}</td>
                                        <td>{{$dato["activos"]}}</td>                       
                                        <td>{!!$dato["diferencial"]!!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--MODAL INFORMATIVO DETALLES DE LOS MOTIVOS DE EGRESOS-->
            <div class="modal fade" id="modal-motivos-egresos" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-users"></i> Detalles de los motivos de Egresos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm compact text-sm table-bordered table-striped text-center" id="datatable-motivosEgresos"> 
                                <thead style="background-color:#1B3155; color:white; font-weight:bold; text-align:left;">
                                    <tr>
                                        <th>Cartera</th>
                                        <th>Motivo de Egreso</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datos as $dato)
                                    @if($dato['motivos']!= null)
                                        <tr class="{{ $dato['clase_bg'] }}">
                                            <td>{{ $dato['cartera'] }}</td>
                                            @foreach ($dato['motivos'] as $key => $motivo)
                                                <td><b>{{ $key }}</b></td>
                                                <td>{{ $motivo }}</td>
                                            @endforeach      
                                        </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

             <!--MODAL INFORMATIVO DETALLES DE LOS MOTIVOS DE EGRESOS-->
            <div class="modal fade" id="modal-indicadores" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-users"></i> Indicadores Por Campaña</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm compact text-sm table-bordered table-striped text-center" id="datatable-indicadores"> 
                                <thead style="background-color:#BE3A8D; color:white; font-weight:bold; text-align:left;">
                                    <tr>
                                        <th>Cartera</th>
                                        <th>Rotacion</th>
                                        <th>Indice Rotacion</th>
                                        <th>Absentismo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datos as $dato)
                                        <tr>
                                            <td>{{ $dato['cartera'] }} </td>
                                            <td>{{ $dato['rotacion'] }}%</td>
                                            <td>{{ $dato['indice'] }} %</td>   
                                            <td class="text-danger"><b >{{ $dato['absentismo'] }} %</b></td>   
                                        </tr>                                    
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>       
</div>
@endsection

@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<style>
    #datatable-skills_wrapper .dataTables_filter {
        text-align: left;
    }
    #datatable-skills_wrapper .dataTables_filter label {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    #datatable-skills_wrapper .dataTables_filter input {
        width: 100%;
        margin-left: 0;
        display: block;
    }
</style>
@endpush

@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/jquery/jquery.blockUI.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/chart.js/Chart.min.js')}}" type="text/javascript"></script>
<script>
var asset = '{!! asset("img/loading-icon-transparent-background-12.jpg") !!}'; // declaras la ruta que apunta a public

//arreglo para el chat de barras
var areaChartData = {

labels: {!! $chartLabels !!},
        datasets: [
        {
        label: "Requeridos",
                backgroundColor: "#1B3155",
                borderColor: "#1B3155",
                order: 2,
                data: {!! $chartRequeridos !!},
        },
        {
        label: "Activos",
                backgroundColor: "#BE3A8D",
                borderColor: "#BE3A8D",
                order: 2,
                data: {!! $chartActivos !!},
        },
        {
        label: "% Cumplimiento",
                type: "line",
                order:1,
                backgroundColor: "rgba(255, 193, 7, 0.2)",
                borderColor: "#FFC107",
                borderWidth: 2,
                fill: false,
                pointBackgroundColor: "#FFC107",
                pointBorderColor: "#FFC107",
                pointRadius: 4,
                data: {!! $chartcumplimiento !!},
                yAxisID: "y-cumplimiento",
        },
        ],
};
//Arreglo para el chart de egresos
var chartColors = [
        "#BE3A8D",
        "#00a65a",
        "#8C8F93",
        "#1B3155",
        "#3c8dbc",
        "#d2d6de",
        "#BE3A8D",
        "#6f42c1",
        "#20c997",
        "#e83e8c",
];
var chartEgresos = {
labels: {!! $chartEgresosLabels ?? '[]' !!},
        datasets: [
        {
        label: "Total Egresos",
                backgroundColor: chartColors,
                data: {!! $chartEgresosData ?? '[]' !!},
        },
        ],
};</script>
<script src="{{asset('js/dashboard/dashboardPersonal.js')}}" type="text/javascript"></script>
@endpush