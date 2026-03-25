@extends('layouts.main')
@section('title', 'Gestion de personal|Uploads')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('personal','active')
@section('personal.upload','active')
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
                        <li class="breadcrumb-item">Gestión de Personal</li>
                        <li class="breadcrumb-item active">Importar</li>
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
                            <h3 class="card-title">Importar Listado de Personal</h3>                            
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">                                                          
                            <br/>     
                            <form action="{{route('personal.masiva.store')}}" method="POST" id="create-agent" class="form-horizontal form-label-left" enctype='multipart/form-data'>                            
                                @csrf
                                <div class="form-group">
                                    <label class="col-form-label" for="archivo">Archivo de Personal <span class="required" style="color:red;">*</span></label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" required id="archivo" name="archivo">
                                            <label class="custom-file-label" for="exampleInputFile">Buscar Archivo en...</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit"  class="btn btn-primary cargamasiva"><i class="fa fa-save"></i> Importar</button>
                                        </div>
                                    </div>
                                </div>                          
                            </form>
                            <br>                            
                            <div>
                                <h5 Style="color: red;"> Recomendaciones a considerar al momento de subir el archivo:</h5>
                                <p>Utilice el formato suministrado para cargar los registros. Si no lo tienes Descarga <a href="{{\Storage::disk('public')->url('MasivaPersonal.xlsx')}}">Aqui</a></p>
                                <p>No realice ninguna modificación en las cabeceras de las columnas y siga el glosario de datos que a continuación puede descargar desde <a href="{{\Storage::disk('public')->url('glosario.xlsx')}}">Aqui</a></p>
                                <p></p>

                            </div>                             

                            <div class="ln_solid"></div>
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
<script src="{{asset('js/gestion_personal/upload.js')}}" type="text/javascript"></script>
@endpush