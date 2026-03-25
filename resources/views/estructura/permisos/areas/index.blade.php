@extends('layouts.main')
@section('title', 'Gestion de personal|Ajustes|Gerencias')
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
                        <li class="breadcrumb-item active">Editar Unidades/Campañas-Segmentos/Skills</li>
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
                            <h3 class="card-title">Listado de Gerencia/Area de trabajo y campañas</h3> <a href="{{route('personal.ajustes.areas.create')}}" class="btn btn-primary float-right" id="add" data-toggle="tooltip" data-placement="left" title="Nuevas Estrcutura"><i class="fa fa-plus-circle"></i></a>                           
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
                                    {!! Form::open(['route' => ['areas.carteras.update','gerencia'],'method' => 'PUT','id' => 'gerencias_update','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                    {!! Form::token() !!} 
                                    <div class="row">
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('area_trabajo', 'Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    <select id="area_trabajo" name="area_trabajo" class="required form-control" data-href='{{route('selector.areas.estructuras')}}'>
                                                        <option value="">[Seleccione]</option> 
                                                        @foreach($areas as $area)
                                                        <option value="{{$area->id}}"data-nombre='{{$area->nombre_campana}}'>{{$area->nombre_campana}}</option>
                                                        @endforeach
                                                    </select> 
                                                </div>  
                                            </div>
                                        </div>                                        
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('gerenciaedit', 'Editar Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::text('gerenciaedit',old('gerenciaedit'),['class' => 'form-control','id' =>'gerenciaedit' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>                                       
                                        <div class="col-md-12 text-center">                                     
                                            <button type="button"class="btn btn-lg btn-primary" id="buttongerencia" disabled  onclick="activemodal('gerencia');"                data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class="fas fa-sync"></i></button>   
                                            <button type="button"class="btn btn-lg btn-danger"  id="buttongerenciadelete" disabled  onclick="activemodaldelete('gerencia');"    data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>   
                                        </div>                      
                                        {!! Form::close() !!}
                                    </div>
                                </div>  
                                <div class="col-md-12 hidendivs" id="divunidades" style="display:none;">
                                    {!! Form::open(['route' => ['areas.carteras.update','unidades'],'method' => 'PUT','id' => 'unidades_update','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                    {!! Form::token() !!} 
                                    <div class="row">
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('area_trabajo', 'Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    <select id="area_trabajo" name="area_trabajo" class="required form-control" data-href='{{route('selector.areas.estructuras')}}'>
                                                        <option value="">[Seleccione]</option> 
                                                        @foreach($areas as $area)
                                                        <option value="{{$area->id}}">{{$area->nombre_campana}}</option>
                                                        @endforeach
                                                    </select>                                                    
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('carteras', 'Unidad / Campaña', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::select('carteras',[],old('carteras'),['class' => 'form-control','id' =>'carteras', 'data-href'=>route('selector.carteras'),'required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('cartera', 'Editar la Unidad/Campaña', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::text('carteraedit',old('carteraedit'),['class' => 'form-control','id' =>'carteraedit' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <div class="form-group">
                                                {!! Form::label('estatuscartera', 'Estatus la Unidad/Campaña', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::select('estatuscartera',["1"=>"ACTIVO","2"=>"INACTIVO"],old('estatuscartera'),['class' => 'form-control','id' =>'estatuscartera','required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div> 
                                            </div>
                                        </div> 
                                        <div class="col-md-12 text-center">                                     
                                            <button type="button"class="btn btn-lg btn-primary" id="buttonunidad"       disabled  onclick="activemodal('unidad');" data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class="fas fa-sync"></i></button>   
                                            <button type="button"class="btn btn-lg btn-danger"  id="buttonunidaddelete" disabled  onclick="activemodaldelete('unidad');"   data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>   
                                        </div> 
                                        {!! Form::close() !!}
                                    </div>
                                </div>     
                                <div class="col-md-12 hidendivs" id="divskill" style="display:none;">                                
                                    {!! Form::open(['route' => ['areas.carteras.update','skill'],'method' => 'PUT','id' => 'skill_updates','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                    {!! Form::token() !!} 
                                    <div class="row">
                                        <div class="col-md-4">                                    
                                            <div class="form-group">
                                                {!! Form::label('area_trabajo', 'Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    <select id="area_trabajo" name="area_trabajo" class="required form-control" data-href='{{route('selector.areas.estructuras')}}'>
                                                        <option value="">[Seleccione]</option> 
                                                        @foreach($areas as $area)
                                                        <option value="{{$area->id}}"data-nombre='{{$area->nombre_campana}}'>{{$area->nombre_campana}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-4">                                    
                                            <div class="form-group">
                                                {!! Form::label('carteras', 'Unidad / Campaña', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::select('carteras',[],old('carteras'),['class' => 'form-control','id' =>'carteras' ,'data-href'=>route('selector.carteras.estructuras'),'required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-4">                                    
                                            <div class="form-group">
                                                {!! Form::label('skill', 'Segmento/Skill', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::select('skill',[],old('skill'),['class' => 'form-control','id' =>'skill' ,'required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('editskills', 'Segmento / Skill', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::text('editskills',old('skills'),['class' => 'form-control','id' =>'editskills' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>  
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('estatusskill', 'Estatus Del Skill', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::select('estatusskill',["Activo"=>"ACTIVO","Inactivo"=>"INACTIVO"],old('estatusskill'),['class' => 'form-control','id' =>'estatusskill','required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>  
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('codceco', 'COD. Centro de Costo', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::text('codceco',old('codceco'),['class' => 'form-control input-number','maxlength'=>'5','id' =>'codceco' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-6">                                    
                                            <div class="form-group">
                                                {!! Form::label('nombceco', 'Centro de Costo', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                                <div class="input-group mb-3">                                        
                                                    {{ Form::text('nombceco',old('nombceco'),['class' => 'form-control','id' =>'nombceco' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-center">                                     
                                            <button type="button"class="btn btn-lg btn-primary" id="buttonskill" disabled  onclick="activemodal('skill');"         data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Actualizar"> <i class=" fa fa-sync"></i></button>   
                                            <button type="button"class="btn btn-lg btn-danger"  id="buttonskilldelete" disabled  onclick="activemodaldelete('skill');"   data-toggle="modal"  data-toggle="tooltip" data-placement="top" title="Eliminar"> <i class=" fa fa-trash"></i></button>   
                                        </div>                                        
                                        {!! Form::close() !!}
                                    </div>                                
                                </div>                                 
                                <div class="modal fade" id="modalconfirmacion" tabindex="-1" role="dialog" aria-labelledby="tittle_modal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="tittle_modal"></h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body" id="modalcontent"></div>
                                            <input type="hidden" id="sendform" name="sendform">
                                            <div class="modal-footer">                            
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                <button type="button" class="btn btn-primary" onclick="sendData();">Aceptar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>  
                                <div class="modal fade" id="modaldeleted" tabindex="-1" role="dialog" aria-labelledby="tittle_modal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="tittle_modaldeleted"></h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            {!! Form::open(['route' => ['personal.ajustes.areas.deleted'],'method' => 'DELETE','id' => 'senddeleted','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                            {!! Form::token() !!} 
                                            <div class="modal-body"> 
                                                <p id="modalcontent_deleted"></p>
                                            <input type="hidden" id="sendformdelete"    name="sendformdelete">
                                            <input type="hidden" id="delgerencia" name="delgerencia">
                                            <input type="hidden" id="delunidad"   name="delunidad">
                                            <input type="hidden" id="delskill"    name="delskill">                                           
                                            </div>
                                            <div class="modal-footer">                            
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary" >Aceptar</button>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
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
{!!Html::style('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')!!}
{!!Html::style('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')!!}
{!!Html::style('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')!!}

@endpush

@push('scripts')   
<!-- DataTables  & Plugins -->
{!! Html::script('plugins/datatables/jquery.dataTables.min.js') !!}
{!! Html::script('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') !!}
{!! Html::script('plugins/datatables-responsive/js/dataTables.responsive.min.js') !!}
{!! Html::script('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') !!}
{!! Html::script('plugins/datatables-buttons/js/dataTables.buttons.min.js') !!}
{!! Html::script('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') !!}

{!! Html::script('plugins/pdfmake/pdfmake.min.js') !!}
{!! Html::script('plugins/pdfmake/vfs_fonts.js') !!}
{!! Html::script('plugins/datatables-buttons/js/buttons.html5.min.js') !!}
{!! Html::script('plugins/datatables-buttons/js/buttons.print.min.js') !!}
{!! Html::script('plugins/datatables-buttons/js/buttons.colVis.min.js') !!}
{!! Html::script('js/gestion_personal/estructura.js') !!}
@endpush