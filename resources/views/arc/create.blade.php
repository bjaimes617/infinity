@extends('layouts.main')
@section('title', 'Ejecucion ARC')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_arc','menu-is-opening menu-open')
@section('arc','active')
@section('arc.generate','active')

@section('content')
<style>
    select:invalid:focus {
        border-color: #dc3545;
    }
    select:valid:focus {
        border-color: green;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">ARC</li>
                        <li class="breadcrumb-item active">Generar por CI</li>
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
                    <h3 class="card-title">Creación de ARC</h3>
                </div>
                <!-- /.card-header -->   
                <div class="card-body">
                    <form action='{{route('arc.store')}}' method='POST' id="create-arc" target="_blank" class="form-horizontal" enctype="multipart/form-data">                            
                        @csrf                         
                        <div class="row mb-3">                                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="anio">Año <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <input type="text" name="anio" id="anio" required autocomplete="off" class="form-control mesdate">                                     
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="cedula">Cedula de Identidad <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        </div>
                                        <input type="text" name="cedula" id="cedula"  required class="form-control input-number" placeholder="XXXXXXX">     
                                        <button type="submit" class="btn btn-primary btn-flat" id="enviar"
                                                data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                data-toggle="tooltip" data-placement="top" title="Generar">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>                                                                                            

                                    </div> 
                                </div>
                            </div>


                        </div>

                    </form>
                </div>             
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/arc/arc.js')}}" type="text/javascript"></script>
@endpush