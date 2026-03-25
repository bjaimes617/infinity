@extends('layouts.main')
@section('title', 'Inventario|Administracion|Cargas M.')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones_administracion','menu-is-opening menu-open')
@section('inventario.administracion','active')
@section('inventario.administracion.cargas','active')
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
                        <li class="breadcrumb-item">Administracion</li>
                        <li class="breadcrumb-item active">Carga</li>
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
                    <h3 class="card-title">Carga Masiva de Inventario</h3>                            
                </div>
                <form action="{{route('inventario.migracion.administracion.uploads.store')}}" id="uploadfile" method="POST" class="form-horizontal  form-label-left">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="type">Formato a Procesar<span style="color:red">*</span></label>                                 
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-bullhorn"></i></span>
                                        </div>
                                        <select name="type" id="type" required class="form-control select2">                                                   
                                            <option value="">[SELECCIONE]</option>                                            
                                            @foreach($tipo as $key => $tip)
                                            <option value="{{$key}}"> {{$tip}}</option>
                                            @endforeach
                                        </select>                                             
                                    </div> 
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="col-form-label" for="archivo">Cargar Archivo <span class="required" style="color:red;">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="archivo" name="archivo">
                                            <label class="custom-file-label" for="exampleInputFile">Buscar Archivo en...</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Importar</button>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <h5 Style="color: red;"> Recomendaciones a considerar al momento de subir el archivo:</h5>
                                <p>Utilice el formato suministrado para cargar los registros de <b>Monitores</b>. Si no lo tienes Descarga 
                                    <a href="{{\Storage::disk('public')->url('inventario/Formato_carga_Monitores.xlsx')}}">Aqui</a></p>                               
                                <p>Utilice el formato suministrado para cargar los registros de <b>Equipos</b>. Si desea aplicar mas discos y Ram, duplique las Columnas de <b>(DISCO - RAM)</b> y el resto de equipos que no aplique,  mantener el espacio en blanco .Descarga 
                                    <a href="{{\Storage::disk('public')->url('inventario/Formato_carga_Equipos.xlsx')}}">Aqui</a></p>                                                               
                                <p>Para Rellenar ciertos campos en los formatos, utilice el siguiente glosario de datos. Si no lo tienes Descarga 
                                    <a href="{{\Storage::disk('public')->url('inventario/Glosario_datos_inventario.xls')}}">Aqui</a></p>                                                               
                            </div>
                        </div>                    
                    </div>               
                </form>
            </div>            
    </section>
</div>

@endsection
@push('styles')

@endpush
@push('scripts')
<script>
    var asset = '{!! asset("img/loading-icon-transparent-background-12.jpg") !!}'; // declaras la ruta que apunta a public
</script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/jquery/jquery.blockUI.js')}}" type="text/javascript"></script>
<script src="{{asset('js/inventario/upload.js')}}" type="text/javascript"></script>
@endpush