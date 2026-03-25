@extends('layouts.main')
@section('title', 'Ajustes|Gerencia|Agregar Estructura')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('gerencia','active')
@section('personal.ajustes','menu-is-opening menu-open')
@section('personal.ajustes.gerencia','menu-is-opening menu-open')
@section('gerencias.agregar','active')
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
                        <li class="breadcrumb-item active">Gerencias</li>
                        <li class="breadcrumb-item active">Agregar Estructura </li>
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
                            <h3 class="card-title">Agregar Estructura de Nuevas Gerencias, Nuevas Unidades o nuevos Skills</h3>
                        </div> 
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
                                        <a href="#" onclick="displays('divgerencias')" class="small-box-footer">Agregar Nueva Gerencia <i class="fas fa-arrow-circle-right"></i></a>
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
                                        <a href="#" onclick="displays('divunidades')" class="small-box-footer">Agregar Nuevos Departamentos/Campañas  <i class="fas fa-arrow-circle-right"></i></a>
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
                                        <a href="#" onclick="displays('divskill')" class="small-box-footer">Agregar Nuevos Segmentos / Skills <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-12 hidendivs" id="divgerencias" style="display:none;" >    
                                {!! Form::open(['route' => ['personal.ajustes.areas.store','gerencias'],'method' => 'POST','id' => 'area_create','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                {!! Form::token() !!}
                                <div class="col-md-12">                                    
                                    <div class="form-group">
                                        {!! Form::label('area_trabajo', 'Nombre de la Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                        <div class="input-group mb-3">                                        
                                            {{ Form::text('area_trabajo',old('area_trabajo'),['class' => 'form-control','id' =>'gerencia' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                        </div>  
                                    </div>                                        
                                </div>
                                <div class="col-md-12 text-center">                                     
                                    <button type="submit"class="btn btn-lg btn-primary" data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
                                </div>                                    
                                {!! Form::close() !!}     
                            </div>                            
                            <div class="col-md-12 hidendivs" id="divunidades" style="display:none;">
                                {!! Form::open(['route' => ['personal.ajustes.areas.store','unidades'],'method' => 'POST','id' => 'area_create','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                {!! Form::token() !!} 
                                <div class="row">
                                    <div class="col-md-6">                                    
                                        <div class="form-group">
                                            {!! Form::label('gerencia', 'Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                            <div class="input-group mb-3">                                        
                                                {{ Form::select('gerencia',$areas,old('gerencia'),['class' => 'form-control','id' =>'gerencia' ,'required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-6">                                    
                                        <div class="form-group">
                                            {!! Form::label('cartera', 'Unidad / Campaña', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                            <div class="input-group mb-3">                                        
                                                {{ Form::text('cartera',old('cartera'),['class' => 'form-control','id' =>'cartera' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">                                     
                                        <button type="submit"class="btn btn-lg btn-primary" data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
                                    </div>  
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <div class="col-md-12 hidendivs" id="divskill" style="display:none;">                                
                                {!! Form::open(['route' => ['personal.ajustes.areas.store','skill'],'method' => 'POST','id' => 'area_create','files' => true,'class' => 'form-horizontal  form-label-left']) !!}
                                {!! Form::token() !!} 
                                <div class="row">
                                    <div class="col-md-4">                                    
                                        <div class="form-group">
                                            {!! Form::label('area_trabajo', 'Gerencia', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                            <div class="input-group mb-3">                                        
                                                {{ Form::select('area_trabajo', $areas, null,['id' => 'area_trabajo','data-href'=>route('selector.areas'),'class' => 'required form-control','placeholder' => '[TODOS]']) }}
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-4">                                    
                                        <div class="form-group">
                                            {!! Form::label('carteras', 'Unidad / Campaña', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                            <div class="input-group mb-3">                                        
                                                {{ Form::select('carteras',[],old('carteras'),['class' => 'form-control','id' =>'carteras' ,'required','placeholder'=>'[Seleccione]','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-4">                                    
                                        <div class="form-group">
                                            {!! Form::label('skills', 'Segmento / Skill', ['class' => 'col-form-label']) !!} <span class="required" style="color:red;"> * </span> 
                                            <div class="input-group mb-3">                                        
                                                {{ Form::text('skills',old('skills'),['class' => 'form-control','id' =>'skills' ,'required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
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
                                        <button type="submit"class="btn btn-lg btn-primary" data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
                                    </div>  
                                    {!! Form::close() !!}
                                </div>                                
                            </div>                            
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>

            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
</div>
<!-- /.container-fluid -->

@endsection
@push('styles')
{!!Html::style('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')!!}

@endpush

@push('scripts')
{!!Html::script('plugins/jquery-validation/jquery.validate.min.js')!!}
{!!Html::script('plugins/jquery-validation/additional-methods.min.js')!!}
{!!Html::script('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')!!}
{!!Html::script('js/gestion_personal/estructura.js')!!}
@endpush
