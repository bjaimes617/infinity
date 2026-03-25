<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('cantv/images/icon.png') }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Deivis Henriquez deivis(at)vozip.net"/>
        <meta name="copyright" content="Powered by Deivis Henriquez and Braian Jaimes and benjamin pulido. <?php echo date('Y'); ?>">
        <title>Historial del Participante</title>
        {!!Html::style('plugins/fontawesome-free/css/all.min.css')!!}         
        {!!Html::style('plugins/bootstrap/css/bootstrap.css')!!}       


        {!!Html::style('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')!!}
        {!!Html::style('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')!!}
        {!!Html::style('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')!!}
        @stack('styles')        
    </head>
    <body>
        <div class="container body">
            <div class="x_panel"> 
                <div class="x_title">
                    <br/>                    
                    <div class="clearfix"></div>
                </div> 
                <div class="">                    
                    <div class="main_container">
                        <p>  Historial del participante <b>{{$participante->primer_nombre}} {{$participante->primer_apellido}} - {{$participante->cedula_empleado}}</b></p>    
                        <div class="card">     
                            <div class="card-body">
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
                                    <tbody>
                                        @foreach($historial as $his)
                                        <tr>
                                            <td>{{"DGC".$his->RelationCurso->id." ".$his->RelationCurso->Relationskill->nombre_skill}}</td>
                                            <td>{{date('d/m/Y',strtotime($his->RelationCurso->inicio))}}</td>
                                            <td>{{date('d/m/Y',strtotime($his->RelationCurso->finaliza))}}</td>
                                            <td>{{ $his->RelationEstatus->estatus }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>                
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        @push('scripts')   
<!-- DataTables  & Plugins -->
{!! Html::script('plugins/jquery/jquery.min.js') !!}
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

    </body>
    <script type="text/javascript">
        $("#datatable-historial").DataTable({
            "lengthMenu": [10, 20, 50, 75],
            "ordering": true,
            "paging": true,
            "searching": true,
             order: [[0, 'desc']]});
    </script>
</html>
