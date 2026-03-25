@extends('layouts.main')
@section('title', 'Gestion de Participantes')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('cursosmenu','menu-is-opening menu-open')
@section('menu.cursos.participantes','menu-is-opening menu-open')
@section('cursosactive','active')
@section('buscar.index.participantes','active')
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
                        <li class="breadcrumb-item">Gestión de Participantes</li>
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12">
                    <div class="card">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
                        <div class="card-header">
                            <h3 class="card-title">Buscador de Participante </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">  
                            <form action="{{route('buscador.listado.participante')}}" method="post">
                                 <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token" /> 
                            <div class="row">
                                <div class="col-sm-3">                                    
                                    <div class="form-group">
                                        {!! Form::label('Cedula', 'Cedula del Participante', ['class' => 'col-form-label']) !!}
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                                            </div>
                                            {{ Form::number('cedula_empleado', $cedula_empleado,['class' => 'form-control','id' =>'cedula_empleado','data-parsley-required-message']) }}
                                        </div> 
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('Nombre', 'Nombre del Participante', ['class' => 'col-form-label']) !!}
                                       <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        </div>
                                            {{ Form::text('nombre_participante', $nombre_participante,['class' => 'form-control','id' =>'nombre_participante']) }}
                                    </div> 
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        {!! Form::label('&nbsp;', '', ['class' => 'col-form-label']) !!} 
                                        <div class="input-group mb-3">                                      
                                            <button type="submit" class="btn btn-primary" id="busquedapt" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>                                                    
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </form>
                            <div class="col-12" id="tablept"> 
                                <br/>
                                <h6>  Estos resultados son extraidos de forma integra de la base de datos</h6>    
                                 <div class="ln_solid"></div>
                                 <table id="datatable-rec_cparticipantes" class="table table-bordered table-hover text-center">
                                    <thead>
                                        <tr>                                            
                                                <th>Cedula</th>                                                
                                                <th>Nombre Participante</th>
                                                <th>Telefono Movil</th>
                                                <th>Ultimo Curso</th>
                                                <th>Estatus</th>
                                                <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @foreach($participante as $part)
                                        <tr>
                                            <td>{{$part->cedula_empleado}}</td>
                                            <td>{{ucwords($part->primer_nombre)." ".ucwords($part->primer_apellido)}}</td>
                                            <td>{{$part->telefono_movil}}</td>
                                            <td>{{ "DGC".$part->RelationCurso->id." ".$part->RelationCurso->Relationskill->nombre_skill }}</td>
                                            <td>{{ $part->RelationEstatus->estatus }}</td>
                                            <td>
                                                {!!Form::open (['route' => ['participante.destroy',$part->id], 'method' => 'DELETE', 'id' => 'delete_participante'.$part->id]) !!} 
                                        @permission('editar.participantes')
                                        <a href="{{route('participantes.edit',$part->id)}}" class="btn btn-sm btn-primary"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" title="Editar Participante"></i></a>
                                        @endpermission
                                        
                                        @permission('eliminar.participantes')
                                        <a href="#" onclick="if(!confirm('¿Estas seguro que deseas eliminar este participante?'))                                        
                                       { return false; } else {$('#delete_participante'+'{!!$part->id!!}').submit(); }" data-toggle="tooltip" data-placement="top" title="Eliminar Participante" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                       @endpermission
                                        @permission('historial.participante')
                                        <a href="javascript:void(0)" class="btn btn-sm btn-success viewhistory" id="historial{{$i}}" data-name="{{ucwords($part->primer_nombre)}} {{ucwords($part->primer_apellido)}}"><i class="fas fa-eye" data-toggle="tooltip" data-placement="top" title="Ver Historial"></i></a>
                                        @endpermission

                                    <input type="hidden" name="cedula_participante" id="cedula_participante{{$i}}" value="{{$part->cedula_empleado}}">


                                        {!!Form::close() !!} 
                                                </td>
                                        </tr>
                                        @php $i++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="url" value="{{route('historial')}}" id="url">

                                <div class="modal fade" id="viewhistory" tabindex="-1" role="dialog" aria-labelledby="viewhistory_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Historial del Participante <small> <label id="name" ></label></small></h5>&nbsp;
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-history">
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">  
                
                                 <table id="datatable-historial" class="table table-bordered table-hover text-center">
                                    <thead>
                                        <tr>                                            
                                                <th>Curso</th>                                                
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Fin</th>
                                                <th>Estatus Final</th>
                                        </tr>
                                    </thead>
                                </table>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close-history">Close</button>
            </div>
        </div>
    </div>
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
{!!Html::style('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')!!}
{!!Html::style('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')!!}
{!!Html::style('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')!!}
@endpush

@push('scripts')   
<!-- DataTables  & Plugins -->
{!!Html::script('plugins/datatables/jquery.dataTables.js')!!}
{!!Html::script('plugins/datatables-bs4/js/dataTables.bootstrap4.js')!!}
{!!Html::script('plugins/datatables-responsive/js/dataTables.responsive.js')!!}
{!!Html::script('plugins/datatables-responsive/js/responsive.bootstrap4.js')!!}
{!!Html::script('plugins/datatables-buttons/js/dataTables.buttons.min.js')!!}
{!!Html::script('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')!!}
{!!Html::script('plugins/jszip/jszip.min.js')!!}
{!!Html::script('plugins/pdfmake/pdfmake.min.js')!!}
{!!Html::script('plugins/pdfmake/vfs_fonts.js')!!}
{!!Html::script('plugins/datatables-buttons/js/buttons.html5.min.js')!!}
{!!Html::script('plugins/datatables-buttons/js/buttons.print.min.js')!!}
{!!Html::script('plugins/datatables-buttons/js/buttons.colVis.min.js')!!}
{!!Html::script('js/historial/historial.js')!!}
<script>
    
    function delete_submit(val){
    console.log($('#delete_participante' + val));
    $('#delete_participante' + val).submit();
    }

    $('#datatable-rec_cparticipantes').DataTable( {
    responsive: true,
            "ordering": true,
            "autoWidth": false,
            "lengthMenu": [10, 20, 50, 75],
            "bPaginate": true,
} );

</script>
<script>
    @for($j=1; $j <= $cantidad; $j++)
    $("#historial{{$j}}").click(function () {

    var url = $("#url").val();
    var data = {
        cedula_participante: $("#cedula_participante{{$j}}").val(),
        _token: $("#token").val()
    };
   
        if ($.fn.dataTable.isDataTable('#datatable-historial')) {
            $('#datatable-historial').DataTable().destroy();
        }
        $('#datatable-historial').DataTable({
            "initComplete": function (settings, json) {

            },
            responsive: true,
            "ordering": true,
            "autoWidth": false,
             order: [[0, 'desc']],
            "ajax": {
                "url": url,
                "dataType": "json",
                "type": "POST",
                "data": data,
                dataSrc: ""
            },
            "lengthMenu": [10, 20, 50, 75],
            "bPaginate": true,
            "bProcessing": true,
            "columns": [                           
                {mData: 'curso'},              
                {mData: 'fecha_inicio'},  
                {mData: 'fecha_fin'},
                {mData: 'estatus'}

            ]
        });    
});
    @endfor
</script>
@endpush