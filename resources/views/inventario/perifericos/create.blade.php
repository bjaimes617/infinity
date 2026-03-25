@extends('layouts.main')
@section('title', 'Registro de Perifericos en Stock')
@section('menu_inventario','menu-is-opening menu-open')
@section('inventario','active')
@section('menu_periferico','menu-is-opening menu-open')
@section('inventario.periferico','active')
@section('inventario.create.periferico','active')
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
                        <li class="breadcrumb-item">Inventario</li>
                        <li class="breadcrumb-item active">Perifericos</li>
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
                    <h3 class="card-title">
                        <i class="fas fa-cubes"></i>
                        Registro de Perifericos en Stock
                    </h3>
                </div>                        
                <form action="{{route('inventario.store.periferico')}}" method="POST" id="periferico_create"  class="form-horizontal form-label-left">
                    <div class="card-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-12 col-form-label" for="stock">Stock a Cargar <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="stock" id="stock" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($sede as $item)
                                            <option value="{{$item->id}}">{{$item->tipo}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="cantidad">Cantidad <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <input type="number" id="cantidad" name="cantidad" min="1" required class="form-control">                                                                                               
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-12 col-form-label" for="periferico">Tipo de Periferico <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-mouse"></i></span>
                                        </div>
                                        <select name="periferico" id="periferico" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($periferico as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-12 col-form-label" for="conexion">Conexion Principal <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-usb"></i></span>
                                        </div>
                                        <select name="conexion" id="conexion" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($conexion as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-lg btn-block btn-primary"  data-toggle="tooltip" data-placement="top" title="Registrar"> <i class="fa fa-save"></i></button>   
                    </div>
                </form>
            </div> 
        </div>
    </section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/inventario/autocomplete.js')}}"></script>
<script src="{{asset('js/inventario/inventario.js')}}"></script>
@endpush