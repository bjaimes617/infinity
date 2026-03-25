@extends('layouts.main')
@section('title', 'Gestion de personal|Calendario Finalizaciones')
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
                        <li class="breadcrumb-item">Gestión de Personal</li>
                        <li class="breadcrumb-item">Renovaciones y Finalizaciones</li>                      
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
                    <h3 class="card-title">Calendario de Renovaciones y Finalizaciones </h3>  
                </div>
                <!-- /.card-header -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div id="calendar"></div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/fullcalendar/main.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/fullcalendar/main.js')}}" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var today = new Date(); // fecha actual

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: @json($event),       
        eventColor: '#8C8F93', // color general, puedes quitar si usas colores por evento
        // Personalizar el contenido del evento
        eventContent: function(info) {
            var eventDate = new Date(info.event.start);
            var titleParts = info.event.title.split('//');
            var primerNombre = titleParts[0] || '';
            var primerApellido = titleParts[1] || '';
            var skill = titleParts[2] || '';
            var descripcion = titleParts[3] || '';

            // Crear el contenido personalizado
            var customHtml = `
                <div style="font-weight: bold;">${primerNombre} ${primerApellido}</div>
                <div>${skill}</div>
                <div>${descripcion}</div>
            `;

            return { html: customHtml };
        },

        // Cambiar colores según la fecha
        eventDidMount: function(info) {
            var eventDate = new Date(info.event.start);
            if (eventDate < today) {
                // Aplica el color predeterminado para eventos pasados
                info.el.style.backgroundColor = '#910101ffff';
                info.el.style.color = 'white'; // o el color que prefieras para texto
            } else {
                // Si no es pasado, usa los colores personalizados si existen
                info.el.style.backgroundColor = info.event.extendedProps.backgroundColor || '#1B3155';
                info.el.style.color = info.event.extendedProps.textColor || 'white';
            }
        },

        // Manejar clics en eventos
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            window.open(info.event.url, '_blank');
        }
    });

    calendar.render();
});

</script>

@endpush