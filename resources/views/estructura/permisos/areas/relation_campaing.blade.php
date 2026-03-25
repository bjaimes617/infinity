@extends('layouts.main')
@section('title', 'Gestion de personal|Ajustes|Gerencias|Relacionar')
@section('gestion_personal','menu-is-opening menu-open')
@section('gestion_personal_active','active')
@section('menu_personal','menu-is-opening menu-open')
@section('gerencia','active')
@section('personal.ajustes','menu-is-opening menu-open')
@section('personal.ajustes.gerencia','menu-is-opening menu-open')
@section('gerencias.relation','active')


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
                        <li class="breadcrumb-item ">Gerencia</li>
                        <li class="breadcrumb-item active">Relacionar</li>
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
                            <h3 class="card-title">Inclusión de Nueva Gerencia/Area de trabajo</h3>                            
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body text-right">  
                            {!! Form::open(['route' => 'personal.ajustes.campaing.store','method' => 'post','id' => 'area_trabajo','class' => 'form-horizontal  form-label-left','data-parsley-validate']) !!}
                            {!! Form::token() !!}
                             <div class="form-group row">  
                                {!! Form::label('area_trabajo', 'Gerencia', ['class' => 'col-sm-3 col-form-label']) !!} <span style="color:red">*</span>                       
                                <div class="col-sm-6">
                                    {{ Form::select('area_trabajo',$areas,old('area_trabajo'), ['class' => 'form-control','required','id' => 'area_trabajo','data-parsley-required-message' => 'Este campo es obligatorio','placeholder' => '[Seleccione]']) }}                                                                             
                                </div>
                            </div> 
                            <div class="form-group row">                                
                                {!! Form::label('cartera', 'Unidad/Campaña', ['class' => 'col-sm-3 col-form-label']) !!} <span style="color:red">*</span>                         
                                <div class="col-sm-6">
                                    {{Form::text('cartera',null,['class'=>'form-control','id'=>'cartera','required'=>'required','placeholder'=>'Unidad / Campaña'])}} 
                                </div>                                
                            </div>
                             <div class="form-group row">  
                                {!! Form::label('codceco', 'Cod CECO', ['class' => 'col-sm-3 col-form-label']) !!} <span style="color:red">*</span>                       
                                <div class="col-sm-6">
                                {{ Form::text('codceco',null,['class' => 'form-control input-number','required','maxlength'=>'5','id' => 'codceco','required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                </div>
                            </div>
                            <div class="form-group row">  
                                {!! Form::label('codceco', 'Nombre CECO', ['class' => 'col-sm-3 col-form-label']) !!} <span style="color:red">*</span>                       
                                <div class="col-sm-6">
                                {{ Form::text('nomceco',null,['class' => 'form-control','required','id' => 'nomceco','required','data-parsley-required-message' => 'Este campo es obligatorio']) }}
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <div class="card-footer text-center">
                        <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Guardar Registro"><i class="fas fa-save"></i></button>
                    </div>
                    {!! Form::close() !!}
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
{!! Html::script('js/gestion_personal/gestion.js') !!}
@endpush