@extends('layouts.main')
@section('title', 'Ajustes|Gerencia|Editar Unidad/Campa&ntilde;a')
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
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Editar Relación de Unidad/Camapaña</h3>
                        </div>    
                        
                        {!! Form::open(['route' => ['areas.carteras.update',$carteras->id],'method' => 'put','id' => 'update-cartera','class' => 'form-horizontal form-label-left','data-parsley-validate']) !!}                                                
                        <!-- /.card-header -->
                       
                                            
                    <div class="card-footer text-center">
                        <button type="button" class="btn btn-primary" onclick="$('#update-cartera').submit();"><i class="fas fa-save"></i> Actualizar</button>
                        <button type="button" class="btn btn-danger delete"data-toggle="modal" data-url="{{route('areas.carteras.delete',$carteras->id)}}"><i class="fas fa-trash-alt"></i> Eliminar</button>
                    </div>
                    {!! Form::close() !!}  
                    <!-- /.card-body -->
                </div>
                    <input type="hidden" id="urlReturn" value="{{route('personal.ajustes.areas.index')}}">
                    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="delete_confirm_title" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="user_delete_confirm_title">
                                Borrar Registro
                            </h4>
                             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            ¿Est&aacute; seguro que desea borrar este registro?, Todo el personal que estaba relacionado, quedara sin Unidad/Campaña.
                        </div>
                        <div class="modal-footer">                            
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="delete">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
                <!-- /.card -->
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
{!!Html::script('js/gestion_personal/gestion.js')!!}
@endpush
