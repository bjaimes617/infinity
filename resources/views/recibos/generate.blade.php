@extends('layouts.main')
@section('title', 'Ejecucion Recibos de Pago')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menurecibo','menu-is-opening menu-open')
@section('reciboactive','active')
@section('recibos.creacion.index','active')
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
                        <li class="breadcrumb-item">Recibo de Pagos</li>
                        <li class="breadcrumb-item active">Generar</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header ">
                            <h3 class="card-title">Creación de Recibos de Pagos</h3>
                        </div>
                        <form action="{{route('recibos.creacion.create')}}" method="post" target="_blank" formtarget="_blank" id="create-recibo" enctype="multipart/form-data" class="form-horizontal form-label-left">
                            @csrf
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">                               
                                        <label for="tprecibo">Nomina <span style="color:red">*</span>  </label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-th-list"></i></span>
                                                </div>
                                                <select class="form-control required" name="nomina" id="nomina" required>                                          
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($nomina as $nom)
                                                    <option value="{{$nom->id}}"> {{$nom->nombre_nomina}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" >                               
                                        <label for="tprecibo">Tipo de Recibo <span style="color:red">*</span>  </label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-th-list"></i></span>
                                                </div>
                                                <select class="form-control required" name="tprecibo" id="tprecibo" disabled required>
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($tiponomina as $tiponomina)
                                                    <option value="{{trim($tiponomina->codigo)}}" data-nomina="{{$tiponomina->nomina_id}}"> {{$tiponomina->descripcion}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="corte">Corte y Periodo <span style="color:red">*</span>  </label>
                                        <div class="form-group">                 
                                            <div class="input-group">
                                                <select class="form-control required" name="corte" id="corte" required>                                          
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($cortes as $key => $corte)
                                                    <option value="{{$key}}" >{{$key}} -  {{$corte}}</option>
                                                    @endforeach      
                                                </select>
                                                <input id="mes" name="mes" class="form-control mesdate" autocomplete="off" required>                                             
                                            </div>
                                        </div> 
                                    </div>

                                    <div class="col-md-6">                               
                                        <label for="persona">Personal a Cargo <span style="color:red">*</span>  </label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                                </div>
                                                <select class="form-control required" name="persona" id="persona" required>
                                                    <option value="">[SELECCIONE]</option>
                                                    @foreach($personal as $personals)
                                                    <option value="{{$personals->id}}">{{$personals->primer_nombre}} {{$personals->primer_apellido}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">  
                                        <label for="enviar">&nbsp;</label>
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-primary btn-block" id="enviar" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Generar"><i class="fas fa-file-pdf"></i></button>                                                    
                                        </div>                                                                 
                                    </div>
                                </div>
                            </div>
                        </form>                                                
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
<link rel="stylesheet" type="text/css" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('js/recibos/recibos.js')}}"></script>
@endpush