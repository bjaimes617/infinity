@extends('layouts.main')
@section('title', 'Registro de Monitores')
@section('menu_inventario','menu-is-opening menu-open')
@section('inventario','active')
@section('menu_periferico','menu-is-opening menu-open')
@section('menu_monitores','menu-is-opening menu-open')
@section('inventario.periferico','active')
@section('inventario.perifericos.monitor','active')
@section('inventario.create.monitor','active')
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
                        <li class="breadcrumb-item">Perifericos</li>
                        <li class="breadcrumb-item active">Monitor</li>
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
                        Registro de Monitores en Stock
                    </h3>
                </div>       
                <input type="hidden" id="rutaautocomplete" value="{{route('inventario.autocompletes')}}">                
                <form action="{{route('inventario.store.monitor')}}" method="POST" id="monitor_create"  class="form-horizontal form-label-left">
                    @csrf
                    <div class="card-body">                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="stock">Stock a Cargar <span style="color:red">*</span></label>                                 
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="marcamonitor">Marca <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <input type="text" id="marcamonitor" name="marcamonitor" autocomplete='off' required class="autocomplete form-control"  placeholder='XXXXX'>                                                                                               
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="serial">Serial del Equipo <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <input type="text" id="serial" name="serial" required class="form-control" placeholder='XXXXX'>                                                                                               
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="pulgadas">Pulgadas <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                       
                                        <input type="text" id="pulgadas" name="pulgadas" required step=".5" class="form-control"  placeholder='32.0'>                                                                                               
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="fuente">Fuente de Poder <span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-plug"></i></span>
                                        </div>
                                        <select name="fuente" id="fuente" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>
                                            @foreach(config('app.TipoCable') as $key => $item)
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