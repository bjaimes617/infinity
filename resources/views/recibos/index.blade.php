@extends('layouts.main')
@section('title', 'Ejecucion Recibos de Pago')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menurecibo','menu-is-opening menu-open')
@section('menu.recibos.administracion','menu-is-opening menu-open')
@section('recibos.administracion','active')
@section('reciboactive','active')
@section('recibos.index','active')
@section('content')
<style>
    select:invalid:focus {
        border-color: #dc3545;
    }
    select:valid:focus {
        border-color: green;
    }
</style>
#####################VISTA NO ACTIVA
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Recibo de Pagos</li>
                        <li class="breadcrumb-item active">Ejecucion</li>
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
                            <h3 class="card-title">Ejecucion de Recibos de Pago del Mes de {{ trans('meses.'.date('F',strtotime(now()))) }}</h3>
                        </div>
                        <!-- /.card-header -->   
                        {!! Form::open(['route' => 'recibos.create','method' => 'post','id' => 'create-agent','class' => 'form-horizontal  form-label-left','data-parsley-validate','enctype' => 'multipart/form-data']) !!}
                        {!! Form::token() !!}
                        <div class="card-body row">                                                          
                            <div class="col-md-6">
                                <label for="">Periodo y Nomina a Ejecutar<span style="color:red">*</span></label>
                                <div class="input-group mb-3">                               
                                    <div class="input-group">
                                        {{ Form::select('corte',["1"=>"1ra. Quincena","2"=>"2da. Quincena"], null, ['class' => 'form-control','id' => 'corte','required'=>'required','data-parsley-required-message' => 'Este campo es obligatorio', 'placeholder' => '[CORTE]']) }}
                                         {{ Form::select('nomina',$nomina, null, ['class' => 'form-control','id' => 'nomina','required'=>'required','data-parsley-required-message' => 'Este campo es obligatorio', 'placeholder' => '[NOMINA]']) }}
                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <label for="">Descripcion y Mes de Ejecución<span style="color:red">*</span></label>
                                <div class="input-group mb-3">                               
                                    <div class="input-group">
                                        {{Form::text('descripcion',null,['class'=>'form-control','id'=>'descripcion','required'=>'required','placeholder'=>'Descripcion del periodo'])}}
                                        {{ Form::text('mes'  ,old('mes') ,['class' => 'form-control mesdate' ,'id' =>'mes'   ,'required' ,'data-parsley-required-message' => 'Este campo es obligatorio']) }}                                    
                                    </div>
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <label for="archivo">Archivo de Nomina </label><span class="required">*</span>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="archivo" name="archivo">
                                        <label class="custom-file-label" for="exampleInputFile">Buscar Archivo en...</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="button" onclick="disable();" class="btn btn-primary cargamasiva"><i class="fa fa-save"></i> upload</button>
                                    </div>
                                </div>
                            </div>    
                            <div class="ln_solid"></div>

                            <div class="col-md-12">
                                <br />
                                <h5 Style="color: red;"> Recomendaciones a considerar al momento de subir el archivo:</h5>
                                <p>Utilice el formato suministrado para ejecutar la corrida de los recibos de pago <a href="{{asset('/source/storage/app/public/Formato de carga Recibos de Pago.xls')}}">Aqui</a></p>
                                <p>Si desea amplicar más Asignaciones o Deducciones, duplique las Columnas de <b>COD</b> y <b>MONTO</b>, el resto de empleados que no aplique, rellenar el espacio en blanco del COD Con Guiones <b>-</b></p>                        
                                <p>Coloque el Codigo de la Asignación o Deducción en la Columna <b>COD</b> y el monto respectivo en la columna <b>MONTO</b> con 2 decimales</p>                        
                            </div>  
                        </div>
                        {!! Form::close() !!}
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
{!! Html::style('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')!!}    
{!! Html::style('plugins/select2/css/select2.min.css') !!}
{!! Html::style('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') !!}
@endpush

@push('scripts')
<script>
   var asset = '{{ asset("img/loading-icon-transparent-background-12.jpg") }}'; // declaras la ruta que apunta a public
</script>
<!-- DataTables  & Plugins -->
{!! Html::script('plugins/moment/moment.min.js') !!}
{!! Html::script('plugins/bootstrap-daterangepicker/daterangepicker.js')!!}    
{!! Html::script('plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')!!}
{!! Html::script('plugins/bs-custom-file-input/bs-custom-file-input.min.js') !!}
{!! Html::script('plugins/jquery/jquery.blockUI.js') !!}
{!! Html::script('plugins/inputmask/jquery.inputmask.min.js') !!}
{!! Html::script('plugins/jquery-validation/jquery.validate.min.js') !!}
{!! Html::script('js/recibos/recibos.js') !!}
@endpush