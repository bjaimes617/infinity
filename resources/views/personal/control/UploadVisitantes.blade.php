@extends('layouts.main')
@section('title', 'Gestion de personal|Uploads')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('control.acceso','menu-is-opening menu-open')
@section('acceso','active')
@section('personal.visitantes.masive','active')


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
                        <li class="breadcrumb-item">Control de Acceso</li>
                        <li class="breadcrumb-item active">Visitantes</li>
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
                        <div class="card-header">
                            <h3 class="card-title">Importar Listado de Visitantes</h3>                               
                        </div>                        
                        <form action="{{route('control.visitantes.store')}}" method="POST" id="qrgeneratortable"  class="form-horizontal  form-label-left" enctype='multipart/form-data'>                                
                            @csrf
                            <div class="card-body">  
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="descripcion">Descripción <span style="color:red">*</span></label>                                                                                                                                      
                                            <div class="input-group">  
                                                <input type="text" class="form-control" required autocomplete="off" name="descripcion" id="descripcion"  
                                                       required value="{{old('descripcion')}}" placeholder="Descripcion del Archivo">                                                         
                                            </div>  
                                        </div>
                                    </div>                             
                                    <div class="col-md-6">  
                                        <div class="form-group">
                                            <label class="col-form-label" for="archivo">Archivo de Personal <span class="required" style="color:red;">*</span></label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                                    <label class="custom-file-label" for="archivo">Buscar Archivo en...</label>
                                                </div>
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary cargamasiva"><i class="fa fa-upload"></i> Procesar</button>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-md-12">
                                        <div class="ln_solid"></div>
                                        <h5 Style="color: red;"> Recomendaciones a considerar al momento de subir el archivo:</h5>
                                        <p>Los Visitantes Que sean cargados por este modulo,estaran activos hasta la fecha de expiración indicada en el formato de carga</p>                                
                                        <p>Puedes Eliminar los Registros segun la descripcion Cargada en el formato de carga en el apartado de eliminación.</p>                                
                                        <p>Utilice el formato suministrado para cargar los registros. Si no lo tienes Descarga <a href="{{\Storage::disk('public')->url('FormatoVisitantes.xls')}}">Aqui</a></p>                                                               
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

@endpush

@push('scripts')  
<script>
    var asset = '{!! asset("img/loading-icon-transparent-background-12.jpg") !!}'; // declaras la ruta que apunta a public
</script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/jquery/jquery.blockUI.js')}}" type="text/javascript"></script>
<script src="{{asset('js/control_acceso/upload.js')}}" type="text/javascript"></script>
@endpush
