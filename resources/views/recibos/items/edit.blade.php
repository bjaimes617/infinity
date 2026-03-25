@extends('layouts.main')
@section('title', 'Editar|Asignaciones y Deducciones')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menurecibo','menu-is-opening menu-open')
@section('reciboactive','active')
@section('menurecibo','menu-is-opening menu-open')
@section('menu.recibos.ajustes','menu-is-opening menu-open')
@section('recibos.ajustes','active')
@section('reciboactive','active')
@section('recibos.items','active')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Recibo de Pagos</a></li>
                        <li class="breadcrumb-item">Asignaciones y Deducciones</li>
                        <li class="breadcrumb-item active">Editar</li>
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
                    <h3 class="card-title">Editando Item del Recibo de pago</h3>
                </div>
                <form action="{{route('recibos.items.update',$items->id)}}" method="POST" id="create-items" class="form-horizontal  form-label-left">                        
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.card-header -->
                            <div class="card-body text-right">                                
                                <div class="form-group row">
                                    <label for="cod" class='col-sm-3 col-form-label'>Codigo <span style="color:red">*</span>  </label>
                                    <div class="input-group col-sm-6">                               
                                        <div class="input-group">                                           
                                            <input id="cod" name="cod" class="form-control" value="{{$items->coditem}}" autocomplete="off" required data-href="{{route('recibos.items.check')}}" placeholder="XXXXX">     
                                        </div>
                                    </div> 
                                </div> 
                                <div class="form-group row">
                                    <label for="descripcion" class='col-sm-3 col-form-label'>Descripcion <span style="color:red">*</span>  </label>
                                    <div class="input-group col-sm-6">                               
                                        <div class="input-group">                                           
                                            <input id="descripcion" name="descripcion" value="{{$items->descripcion}}" class="form-control" autocomplete="off" required  placeholder="Descripcion del Item">     
                                        </div>
                                    </div> 
                                </div>
                                <div class="form-group row">
                                    <label for="descripcion" class='col-sm-3 col-form-label'>Tipo de Item <span style="color:red">*</span>  </label>
                                    <div class="input-group col-sm-6">                               
                                        <div class="input-group">                                           
                                            <select id="tipo" name="tipo" class="form-control" required>
                                                <option value="">[SELECCIONE]</option>
                                                <option value="1" @if($items->getOriginal('tipo') == 1) selected @endif >ASIGNACION</option>
                                                <option value="2" @if($items->getOriginal('tipo') == 2) selected @endif >DEDUCCION</option>
                                                <option value="3" @if($items->getOriginal('tipo') == 3) selected @endif >OTRA DEDUCCION</option>
                                                <option value="4" @if($items->getOriginal('tipo') == 4) selected @endif >AUXILIAR DE DEDUCCION</option>
                                            </select>
                                        </div>
                                    </div> 
                                </div>
                            </div>  
                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-block btn-primary" id="save" data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </form>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
</div>
@endsection
@push('styles')

@endpush

@push('scripts')
<script src="{{asset('plugins/jquery-validation/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/jquery-validation/localization/messages_es.js')}}"></script>
<script src="{{asset('js/recibos/items.js')}}"></script>
@endpush
