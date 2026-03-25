@extends('layouts.main')
@section('title','Estructura|Autorizaci&oacute;n|Editar')


@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">  
                        <li class="breadcrumb-item">Estructura</li>
                        <li class="breadcrumb-item ">Autorizaci&oacute;n</li>
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
                    <h3 class="card-title">Autorizar el Segmento/Skill</h3>                            
                </div>
                <form acction="{{route('estructura.autorizacion.update',$id)}}" method="POST" id="autorization_create" class="form-horizontal form-label-left" data-parsley-validate >
                    @method('PUT')
                    @csrf                    
                    <div class='card-body'>
                        <div class="row">                                
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Gerencias</h3>                            
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">                                                                 
                                            <div  class="input-group">
                                                <select class="duallistbox"
                                                        multiple="multiple"  
                                                        id="area" 
                                                        name="area[]"                                                 
                                                        data-href="{{route('selector.areas.estructuras')}}" 
                                                        required="required">
                                                    @foreach($areas as $areaalls)                                       
                                                    <option value="{{$areaalls->id}}" @if(in_array($areaalls->id, $areas_asinadas)) selected @endif> {{$areaalls->nombre_campana}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Unidad/Campaña </h3>                            
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">                                        
                                            <div  class="input-group">
                                                <select class="duallistbox" 
                                                        id="cartera" 
                                                        name="cartera[]" 
                                                        multiple="multiple"  
                                                        data-href="{{route('selector.carteras.estructuras')}}" 
                                                        required="required">
                                                        @foreach($cartera as $cartera)
                                                        <option value="{{$cartera->id}}" @if(in_array($cartera->id, $carteras_asignadas)) selected @endif>{{$cartera->nombre_cartera}} - {{$cartera->RelationGerencias->abrev}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Segmento/Skill </h3>                            
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">                                        
                                            <div  class="input-group">
                                                <select class="duallistbox" 
                                                        id="skill" 
                                                        name="skill[]" 
                                                        multiple="multiple" 
                                                        required="required">
                                                        @foreach($skill as $skills)
                                                        <option value="{{$skills->id}}" @if(in_array($skills->id, $skills_asignadas)) selected @endif>{{$skills->nombre_skill}} - {{$skills->RelationCarteras->RelationGerencias->abrev}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    @permission('estructura.permisos.create')
                    <div class="card-footer text-center">                                     
                        <button type="submit"class="btn btn-lg btn-primary btn-block" data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                    </div>   
                    @endpermission
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
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">  
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.css')}}">  
@endpush

@push('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}"></script>
<script src="{{asset('js/estructura/autorizacion.js')}}" type="text/javascript"></script>
@endpush