@extends('layouts.main')
@section('title', 'Crear ARI / U.T.')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_ari','menu-is-opening menu-open')
@section('ari','active')
@section('generate.ut','active')
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
                        <li class="breadcrumb-item">ARI</li>
                        <li class="breadcrumb-item active">Registrar ARI / U.T.</li>
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
                    <h3 class="card-title">Registrar ARI / Unidad Tributaria <small></small></h3>                            
                </div>
                <div class="card-body">                             
                    <div class="row">                               
                        <div class="col-md-12">
                            <form action="{{route('ut.store')}}" method="post" id="create-ut" class="form-horizontal  form-label-left">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="anio">Año <span style="color:red">*</span></label>  
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <input type="text" id="anio" name="anio" required class="form-control anioactual">                                            
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="trimestre">Trimestres <span style="color:red">*</span></label>  
                                            <div class="input-group">                                                      
                                                <select name="trimestre" id="trimestre" required class="form-control select2" style="width: 100%;">                                                   
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($trimestres as $key => $tri)
                                                    <option value="{{$key}}">{{$tri}}</option>
                                                    @endforeach
                                                </select>                                              
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">                                        
                                            <label class="col-form-label" for="unidad_tributaria">Valor de la U.T <span style="color:red">*</span></label>  
                                            <div class="input-group">                                               
                                                <input type="number" id="unidad_tributaria" name="unidad_tributaria" 
                                                       step='.01' min='0' 
                                                       placeholder='0.00'
                                                       required class="form-control">     

                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">                                      
                                            <div class="input-group">                                      
                                                <button type="submit" class="btn btn-primary btn-block" id="enviar" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Guardar"><i class="fa fa-save"></i> Guardar</button>    
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>   
                    </div>   
                </div>

            </div>
        </div>      
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet"  href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

@endpush
@push('scripts')
<!-- DataTables  & Plugins -->

<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/ari/plugins.js')}}"></script>
@endpush
