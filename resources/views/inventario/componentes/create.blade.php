@extends('layouts.main')
@section('title', 'Registro de Componentes en Stock')
@section('menu_inventario','menu-is-opening menu-open')
@section('inventario','active')
@section('menu_stock','menu-is-opening menu-open')
@section('inventario.stock','active')
@section('inventario.create.stock','active')
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
                        <li class="breadcrumb-item active">Componentes</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section> 
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">  Registro de Componentes en Stock</h3>                    
                </div>
                <form action="{{route('inventario.store.stock')}}" method="POST" id="stock_create"  class="form-horizontal form-label-left">
                    <div class="card-body">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="stock">Stock a Cargar <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-building"></i></span>
                                        </div>
                                        <select name="stock" id="stock" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($sede as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>   
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
                                    <label class="col-sm-12 col-form-label" for="componente">Tipo de Componente <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                
                                        <select name="componente" id="componente" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($componente as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="tipo">Tipo <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <input type="text" id="tipo" name="tipo" required class="form-control" placeholder='I3 | DDR3 | SSD | M2'>                                                                                               
                                    </div> 
                                </div>
                            </div>
                            <div id="viewgeneracion" class="col-md-12" style="display: none;">
                                <div class="form-group">
                                    <label class="col-sm-12 col-form-label" for="generacion">Generacion CPU <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                
                                        <select name="generacion" id="generacion" class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($generacion as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>   
                                            @endforeach                                                
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="capacidad">Limite de Capacidad <span style="color:red">*</span></label>                                 
                                    <div class="input-group">          
                                        <div class="input-group-prepend">
                                            <input type="text" id="capacidad" name="capacidad" required class="form-control" placeholder='3.5'>
                                        </div>
                                        <select name="expresion" id="expresion" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach($expresion as $key => $item)
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
