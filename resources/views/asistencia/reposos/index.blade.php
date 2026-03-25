@extends('layouts.main')
@section('title', 'Asistencia Operativa|Procesamiento Masivo')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_asistencia','menu-is-opening menu-open')
@section('asistencia','active')
@section('menu_administracion','menu-is-opening menu-open')
@section('asistencia.administracion','active')
@section('asistencia.administracion.reposos','active')
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
                        <li class="breadcrumb-item">Asistencia Operativa</li>
                        <li class="breadcrumb-item active">Procesamiento Masivo</li>
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
                    <h3 class="card-title">Procesamiento Masivo de Datos Para la Asistencia Operativa</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('asistencia.reposos.create')}}" method="POST" id="uploadfile" enctype='multipart/form-data'>
                        @csrf
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
                                            <option value="RecMas">Registro Masivo SSO, VAc, PR y PNR </option>                                               
                                            <option value="Feriados" >Feriados Anuales</option>   
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
                                <p>Utilice el formato suministrado para cargar los registros, si no lo tienes Descarga <a href="{{\Storage::disk('public')->url('FormatoReposos.xlsx')}}">Aqui</a></p>
                                <p>No realice ninguna modificación en las cabeceras de las columnas.</p>                        
                                <p>Recuerde Utilizar los Prefijos de asistencia Correctos: VAC - SSO - PNR - PR </p>                        
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

@endpush
@push('scripts')
<script>
    var asset = '{!! asset("img/loading-icon-transparent-background-12.jpg") !!}'; // declaras la ruta que apunta a public
</script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/jquery/jquery.blockUI.js')}}" type="text/javascript"></script>
<script src="{{asset('js/asistencia/upload.js')}}" type="text/javascript"></script>
@endpush