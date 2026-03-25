@extends('layouts.main')
@section('title', 'Modificaciones de Posiciones')
@section('menu_inventario','menu-is-opening menu-open')
@section('menu_posiciones','menu-is-opening menu-open')
@section('inventario.posiciones','active')
@section('inventario','active')
@section('inventario.index.posiciones','active')
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
                        <li class="breadcrumb-item active">Posiciones</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-map-marker"></i>
                               Modificación de Posiciones
                            </h3>
                        </div>      
                        <div class="card-body">                            
                           
                        </div>                        
                    </div>
                </div>               
            </div>    
        </div>
    </section>
</div>
@endsection
@push('styles')
{!!Html::style('plugins/select2/css/select2.css')!!}
{!!Html::style('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')!!}
{!!Html::style('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')!!}
{!!Html::style('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')!!}
@endpush

@push('scripts')   
{!!Html::script('plugins/jquery-validation/jquery.validate.min.js')!!}
{!! Html::script('plugins/select2/js/select2.full.js') !!}
{!!Html::script('plugins/datatables/jquery.dataTables.js')!!}
{!!Html::script('plugins/datatables-bs4/js/dataTables.bootstrap4.js')!!}
{!!Html::script('plugins/datatables-responsive/js/dataTables.responsive.js')!!}
{!!Html::script('plugins/datatables-responsive/js/responsive.bootstrap4.js')!!}
{!!Html::script('plugins/datatables-buttons/js/dataTables.buttons.min.js')!!}
{!!Html::script('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')!!}
{!!Html::script('plugins/jszip/jszip.min.js')!!}
{!! Html::script('js/inventario/validate.init.js') !!}
{!! Html::script('js/inventario/inventario.js') !!}
{!! Html::script('js/inventario/autocomplete.js') !!}
@endpush
