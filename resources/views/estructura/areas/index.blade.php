@extends('layouts.main')
@section('title', 'Estructura|Gerencias')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('gerencia','active')
@section('personal.ajustes','menu-is-opening menu-open')
@section('personal.ajustes.gerencia','menu-is-opening menu-open')
@section('gerencias.list','active')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Ajustes</li>
                        <li class="breadcrumb-item active">Gerencia</li>
                        <li class="breadcrumb-item active">Listado</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Listado de Gerencias/Campañas/Skills</h3> <a href="{{route('estructura.areas.create')}}" class="btn btn-primary float-right" id="add" data-toggle="tooltip" data-placement="left" title="Nueva Estructura"><i class="fa fa-plus-circle"></i></a>                           
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body"> 
                            <div class="row">
                                <div class='col-md-4'> 
                                    <div class="small-box bg-primary">
                                        <div class="inner">
                                            <h3>Gerencias</h3>                                          
                                            <p>Agrupación Principal</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <a href="#" onclick="displays('divgerencias')" class="small-box-footer">Editar Gerencias <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>                                    
                                </div>
                                <div class='col-md-4'>                                    
                                    <div class="small-box bg-lightblue">
                                        <div class="inner">
                                            <h3>Campañas</h3> 
                                            <p>Departamentos de Gerencias</p>
                                        </div>  
                                        <div class="icon">
                                            <i class="fas fa-network-wired"></i>
                                        </div>
                                        <a href="#" onclick="displays('divunidades')" class="small-box-footer">Editar Departamentos/Campañas <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>                                    
                                </div>
                                <div class='col-md-4'>                                    
                                    <div class="small-box bg-maroon">
                                        <div class="inner">
                                            <h3>Skills</h3>                                            
                                            <p>Segmentación de las Unidades</p>
                                        </div>  
                                        <div class="icon">
                                            <i class="fas fa-project-diagram"></i>
                                        </div>
                                        <a href="#" onclick="displays('divskill')" class="small-box-footer">Editar Segmentos/Skills <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>                                    
                                </div>
                                <div class="col-md-12 hidendivs" id="divgerencias" style="display:none;">
                                    <form action="{{route('estructura.carteras.update','gerencias')}}" method="POST" id="gerencias_update" class="form-horizontal form-label-left" data-parsley-validate >                                                
                                        @method('PUT')
                                        @csrf     
                                        <div class="row">                                           
                                            <div class="col-md-6">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                                                                 
                                                    <div class="input-group">     
                                                        <select name="area_trabajo" required id="area_trabajo" class="form-control" data-href='{{route('selector.areas.estructuras')}}'>
                                                            <option value="">[Seleccione]</option>
                                                            @foreach($areas as $area)
                                                            <option value="{{$area->id}}"  data-nombre="{{$area->nombre_campana}}">{{$area->nombre_campana}}</option>
                                                            @endforeach
                                                        </select>                                                    
                                                    </div>  
                                                </div>
                                            </div>  
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="gerenciaedit">Editar Gerencia<span style="color:red">*</span></label>                                 
                                                    <div class="input-group">                                        
                                                        <input type="text" name="gerenciaedit" id="gerenciaedit" required class="form-control"  value="{{old('gerenciaedit')}}" placeholder="Gerencia de XXXXXX">                                           
                                                    </div>  
                                                </div>
                                            </div> 

                                            <div class="col-md-12 text-center">                                     
                                                <button type="submit"class="btn btn-lg btn-primary btn-block" id="buttongerencia" disabled data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class="fas fa-sync"></i></button>   
                                                <button type="button"class="btn btn-lg btn-danger btn-block"  id="buttongerenciadelete" disabled  onclick="activemodaldelete('gerencia');"    data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>   
                                            </div>  
                                        </div>
                                    </form>                                    
                                </div>                            
                                <div class="col-md-12 hidendivs" id="divunidades" style="display:none;">                                    
                                    <form action="{{route('estructura.carteras.update','unidades')}}" method="POST" id="unidades_update" class="form-horizontal form-label-left" data-parsley-validate >                                                
                                        @method('PUT')
                                        @csrf  
                                        <div class="row">
                                            <div class="col-md-6">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                                                                 
                                                    <div class="input-group">     
                                                        <select name="area_trabajo" required id="area_trabajo" class="form-control" data-href='{{route('selector.areas.estructuras')}}'>
                                                            <option value="">[Seleccione]</option>
                                                            @foreach($areas as $area)
                                                            <option value="{{$area->id}}"  data-nombre="{{$area->nombre_campana}}">{{$area->nombre_campana}}</option>
                                                            @endforeach
                                                        </select>                                                    
                                                    </div>  
                                                </div>
                                            </div>
                                            <div class="col-md-6">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="carteras">Departamento / Campaña <span style="color:red">*</span></label>                                                                                 
                                                    <div class="input-group">     
                                                        <select name="carteras" required id="carteras" class="form-control" data-href='{{route('selector.carteras.estructuras')}}'>
                                                            <option value="">[Seleccione]</option>                                                        
                                                        </select>                                                    
                                                    </div>  
                                                </div>
                                            </div>                                        
                                            <hr>
                                            <div class="col-md-6">                                    
                                                <div class="form-group">
                                                    <label class="col-form-label" for="carteras">Departamento / Campaña <span style="color:red">*</span></label>   
                                                    <div class="input-group mb-3">                                        
                                                        <input type="text" name="carteraedit" id="carteraedit" required class="form-control"  value="{{old('carteraedit')}}" placeholder="XXXXXX">                                           
                                                    </div>  
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="carteras">Estatus del Departamento / Campaña <span style="color:red">*</span></label>   
                                                    <div class="input-group mb-3">           
                                                        <select name="estatuscartera" required id="estatuscartera" class="form-control">
                                                            <option value="">[Seleccione]</option>  
                                                            <option value="1">ACTIVO</option>   
                                                            <option value="2">INACTIVO</option>   
                                                        </select>                                                      
                                                    </div> 
                                                </div>
                                            </div> 
                                            <div class="col-md-12 text-center">                                     
                                                <button type="submit"class="btn btn-lg btn-primary btn-block" id="buttonunidad"       disabled  data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class="fas fa-sync"></i></button>   
                                                <button type="button"class="btn btn-lg btn-danger btn-block"  id="buttonunidaddelete" disabled  onclick="activemodaldelete('unidad');"   data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>   
                                            </div> 
                                    </form>
                                </div>
                            </div>     
                            <div class="col-md-12 hidendivs" id="divskill" style="display:none;"> 
                                <form action="{{route('estructura.carteras.update','skill')}}" method="POST" id="skill_updates" class="form-horizontal form-label-left" data-parsley-validate >                                                
                                    @method('PUT')
                                    @csrf  
                                    <div class="row">
                                        <div class="col-md-4">                                    
                                            <div class="form-group">
                                                <label class="col-form-label" for="area_trabajo">Gerencia <span style="color:red">*</span></label>                                                                                 
                                                <div class="input-group">     
                                                    <select name="area_trabajo" required id="area_trabajo" data-href="{{route('selector.areas.estructuras')}}" class="form-control">
                                                        <option value="">[Seleccione]</option>
                                                        @foreach($areas as $area)
                                                        <option value="{{$area->id}}">{{$area->nombre_campana}}</option>
                                                        @endforeach
                                                    </select>                                                    
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-4">                                    
                                            <div class="form-group">
                                                <label class="col-form-label" for="carteras">Departamento / Campaña <span style="color:red">*</span></label>                                                                                 
                                                <div class="input-group">     
                                                    <select name="carteras" required id="carteras" class="form-control" data-href='{{route('selector.carteras.estructuras')}}'>
                                                        <option value="">[Seleccione]</option>                                                        
                                                    </select>                                                    
                                                </div>  
                                            </div>
                                        </div> 
                                        <div class="col-md-4">                                    
                                            <div class="form-group">
                                                <label class="col-form-label" for="skill">Segmento / Skill <span style="color:red">*</span></label>                                                                                 
                                                <div class="input-group">     
                                                    <select name="skill" required id="skill" data-href="{{route('periodo.skill')}}" class="form-control">
                                                        <option value="">[Seleccione]</option>                                                        
                                                    </select>                                                    
                                                </div>  
                                            </div>
                                        </div> 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="editskills">Nombre del Segmento / Skill <span style="color:red">*</span></label>                                 
                                                <div class="input-group">                                        
                                                    <input type="text" name="editskills" id="editskills" required class="form-control"  value="{{old('editskills')}}" placeholder="XXXXXXXXXX">                                           
                                                </div>  
                                            </div>
                                        </div>  
                                        <div class="col-md-6 text-center">
                                            <div class="form-group">
                                                <label class="col-form-label" for="estatusskill">Estatus del Segmento / Skill <span style="color:red">*</span></label>   
                                                <div class="input-group mb-3">           
                                                    <select name="estatusskill" required id="estatusskill" class="form-control">
                                                        <option value="">[Seleccione]</option>  
                                                        <option value="Activo">ACTIVO</option>   
                                                        <option value="Inactivo">INACTIVO</option>   
                                                    </select>                                                      
                                                </div> 
                                            </div>
                                        </div>      
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="codceco">Cod. Centro de Costo <span style="color:red">*</span></label>                                 
                                                <div class="input-group">                                        
                                                    <input type="text" name="codceco" id="codceco" required class="form-control input-number" maxlength='5' value="{{old('codceco')}}" placeholder="000000">                                           
                                                </div>  
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="nombceco">Centro de Costo <span style="color:red">*</span></label>                                 
                                                <div class="input-group">                                        
                                                    <input type="text" name="nombceco" id="nombceco" required class="form-control" value="{{old('nombceco')}}" placeholder="XXXXXXXXXX">                                           
                                                </div>  
                                            </div>
                                        </div>     
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="tipo_formato">Formato que deben utilizar para la Carga de Variables<span style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <select id="tipo_formato" name="tipo_formato[]"
                                                            class="form-control select2" multiple="multiple"
                                                            style="width: 100%;">
                                                          @foreach ($type_format as $key => $value)
                                                              <option value="{{ $key }}">{{ $key }} - {{ $value }}</option>
                                                          @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>                                   
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="tipo_periodo">Periodo de Pago<span style="color:red">*</span></label>                                 
                                                <div class="input-group">    
                                                    <select id="tipo_periodo" name="tipo_periodo" class="form-control" required>
                                                        <option value="">[Seleccione]</option>
                                                        <option value="Semanal">Semanal</option>
                                                        <option value="Quincenal">Quincenal</option>
                                                        <option value="Mixto">Mixto</option>                                                  
                                                    </select>                                             
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="semanal" style="display: none;">
                                            <div class="form-group">
                                                <label class="col-form-label" for="tipo_periodo">Díass Disponibles<span style="color:red">*</span></label>                                 
                                                <div class="input-group">
                                                    <select id="dias" name="dias[]" class="form-control select2" multiple="multiple" style="width: 100%;">
                                                        <option value="lunes">Lunes</option>
                                                        <option value="martes">Martes</option>
                                                        <option value="miercoles">Miercoles</option>
                                                        <option value="jueves">Jueves</option>
                                                        <option value="viernes">Viernes</option>
                                                        <option value="sabado">Sabado</option>
                                                        <option value="domingo">Domingo</option>
                                                    </select>
                                                </div>        
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="quincenal" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-12"><p><i class="far fa-caret-square-down"></i> Rango de la 1ra. Quincena</p></div>
                                                        <div class="col-md-6">                                            
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="desde1">Desde el dia <span style="color:red">*</span></label>                                 
                                                                <div class="input-group">                                        
                                                                    <input type="number" class="form-control" id="desde1" name="desde1" min="1" max="31">
                                                                </div>  
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">                                            
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="hasta1">Desde el dia <span style="color:red">*</span></label>                                 
                                                                <div class="input-group">                                        
                                                                    <input type="number" class="form-control" id="hasta1" name="hasta1" min="1" max="31">
                                                                </div>  
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-12"><p><i class="far fa-caret-square-down"></i> Rango de la 2da. Quincena</p></div>
                                                        <div class="col-md-6">                                            
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="desde2">Desde el dia <span style="color:red">*</span></label>                                 
                                                                <div class="input-group">                                        
                                                                    <input type="number" class="form-control" id="desde2" name="desde2" min="1" max="31">
                                                                </div>  
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">                                            
                                                            <div class="form-group">
                                                                <label class="col-form-label" for="hasta2">Desde el dia <span style="color:red">*</span></label>                                 
                                                                <div class="input-group">                                        
                                                                    <input type="number" class="form-control" id="hasta2" name="hasta2" min="1" max="31">
                                                                </div>  
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-center"><br><br>                                     
                                            <button type="submit" class="btn btn-lg btn-primary btn-block" id="buttonskill" disabled  data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                                            <button type="button" class="btn btn-lg btn-danger btn-block"  id="buttonskilldelete" disabled  onclick="activemodaldelete('skill');"   data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>   
                                        </div> 
                                    </div>   
                                </form>
                            </div>                                                          
                            <form action="{{route('estructura.areas.deleted')}}" method="POST" id="senddeleted" class="form-horizontal form-label-left" data-parsley-validate >                                                
                                @method('DELETE')
                                @csrf     
                                <input type="hidden" id="sendformdelete"    name="sendformdelete">
                                <input type="hidden" id="delgerencia" name="delgerencia">
                                <input type="hidden" id="delunidad"   name="delunidad">
                                <input type="hidden" id="delskill"    name="delskill">  
                            </form>
                        </div>                            
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.css')}}">  
@endpush

@push('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('js/estructura/estructura.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}"></script>
@endpush
