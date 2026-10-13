@extends('layouts.main')
@section('title', 'Evaluacion de Habilidades|Añadir')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_habilidades','menu-is-opening menu-open')
@section('habilidades','active')
@section('habilidades.evaluacion.create','active')
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
                        <li class="breadcrumb-item">Gestión de Habilidades</li>
                        <li class="breadcrumb-item active">Añadir Evaluación</li>
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
                    <h3 class="card-title">Agregar Nueva Evaluación </h3>
                </div>
                <!-- /.card-header -->                                      
                <div class="card-body">                                
                    <div class="bs-stepper">
                        <div class="bs-stepper-header" role="tablist">
                            <!-- your steps here -->
                            <div class="step" data-target="#evaluacion-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="evaluacion-part" id="evaluacion-part-trigger">
                                    <span class="bs-stepper-circle"><i><i class="fa fa-file"></i></i></span>
                                    <span class="bs-stepper-label">Evaluación</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step" data-target="#categorias-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="categorias-part" id="categorias-part-trigger">
                                    <span class="bs-stepper-circle"><i><i class="fa fa-list"></i></i></span>
                                    <span class="bs-stepper-label">Categorias</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step" data-target="#range-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="range-part" id="range-part-trigger">
                                    <span class="bs-stepper-circle"><i><i class="fas fa-chart-line"></i></i></span>
                                    <span class="bs-stepper-label">Rangos</span>
                                </button>
                            </div>                            
                        </div>   
                        <form method="POST" id="evaluacion_create" action="{{route('habilidades.evaluacion.store')}}" class="form-horizontal form-label-left">
                            @csrf
                            <div class="bs-stepper-content">
                                <input type="hidden" value="" name="total_sum" id="total_sum">
                                <div id="evaluacion-part" class="content" role="tabpanel" aria-labelledby="evaluacion-part-trigger">
                                    <div class="row mb-3">
                                        <div class="col-md-12">                                    
                                            <div class="form-group">
                                                <label for="nombre_evaluacion" class="col-form-label">Nombre de la Evaluación</label> <span class="required" style="color:red;"> * </span>           
                                                <div class="input-group">                                        
                                                    <input type="text" id="nombre_evaluacion" name="nombre_evaluacion" value="{{old('nombre_evaluacion')}}" placeholder="Nombre de la Evaluacion" class="form-control" id="nombre_evaluacion" required data-parsley-required-message="Este campo es obligatorio">
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="cargo_evaluador">Personal que Evalua <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="cargo_evaluador[]" id="cargo_evaluador" required 
                                                            multiple="multiple" class="form-control select2">
                                                        @foreach($cargo_evaluadores as $evaluador)
                                                        <option value="{{$evaluador->id}}">{{$evaluador->nombre_cargo}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="cargo_evaluar">Personal Evaluado  <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group ">
                                                    <select name="cargo_evaluar[]" id="cargo_evaluar" required  multiple="multiple" 
                                                            class="form-control select2">
                                                        @foreach($cargo_evaluados as $index => $evaluados)
                                                        <option value="{{$index}}">{{$evaluados}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="gerencia">Gerencia <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <select name="gerencia[]" id="gerencia" required
                                                            data-href="{{route('selector.areas.estructuras')}}" multiple="multiple" class="form-control duallistbox">                                                                                                              
                                                        @foreach($gerencias as $gerencia)
                                                        <option value='{{$gerencia->id}}'>{{$gerencia->nombre_campana}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="cartera">Unidad/Campaña <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <select name="cartera[]" id="cartera" required  multiple="multiple"
                                                            data-href="{{route('selector.carteras.estructuras')}}" class="form-control duallistbox">
                                                        <option value=''> [SELECCIONE GERENCIA]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label" for="skill">Segmento/Skill <span style="color:red">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <select name="skill[]" id="skill" required multiple="multiple"                                                                                                                 
                                                            class="form-control duallistbox"  data-href="{{route('search.personal.cargo')}}" style="width: 100%;">
                                                        <option value=''> [SELECCIONE GERENCIA]</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">  
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="rango_eval" class="col-form-label">Periodo para Realizar la Evaluación</label> <span class="required" style="color:red;"> * </span>           
                                                        <div class="input-group">     
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            </div>                                   
                                                            <input type="text" name="rango_eval" class="form-control daterange" id="rango_eval" placeholder="[FECHA]" required data-parsley-required-message="Este campo es obligatorio">
                                                        </div>  
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="valor_max" class="col-form-label">Valor maximo en %</label> <span class="required" style="color:red;"> * </span>           
                                                        <div class="input-group">     
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-percent"></i></span>
                                                            </div>                                   
                                                            <input type="number" step="0.001" min="0.00" placeholder="0.000" 
                                                                required class="form-control valor-base" id="valor_max" name="valor_max">
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="btn-group  btn-block float-right">
                                                <button type="button"class="btn btn-primary validateNext" onclick="stepper.next()"  data-toggle="tooltip" data-placement="top" title="Siguiente"> <i class="fa fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="categorias-part" class="content" role="tabpanel" aria-labelledby="categorias-part-trigger">
                                    <input type="hidden" id="totalcategorias" name="totalcategorias" value="0">
                                    <input type="hidden" id="totalcategorias_true" name="totalcategorias_true" value="0">
                                    <input type="hidden" id="totalparametros" name="totalparametros" value="0">
                                    <input type="hidden" id="totalparametros_true" name="totalparametros_true" value="0">
                                    <input type="hidden" id="totalrangos" name="totalrangos" value="0">
                                    <input type="hidden" id="totalrangos_true" name="totalrangos_true" value="0">
                                    <div class="row mb-3">
                                        <div class="col-md-1">
                                            <label for="add-category" class="col-form-label">&nbsp;</label> 
                                            <button type="button" class="btn btn-primary btn-block" id="add-category" 
                                                    onclick="AddCategoryEvaluacion();" data-conteo="0"
                                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." 
                                                    data-toggle="tooltip" data-placement="top" title="Agregar Nueva Categoria"><i class="fas fa-plus-circle"></i></button>                                                    
                                        </div>
                                        <div class="col-md-11">                                            
                                            <div class="row mb-3">                                                
                                                <div class="col-md-6">                                    
                                                    <div class="form-group">
                                                        <label for="valor_max" class="col-form-label">Valor maximo de la evaluación en %</label> <span class="required" style="color:red;"> * </span>           
                                                        <div class="input-group mb-3">     
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="fa fa-percent"></i></span>
                                                            </div>                                   
                                                            <input type="number" step="0.001" min="0.00" placeholder="0.000" disabled class="required form-control valor-base" id="valor_max2" name="valor_max2">
                                                        </div>  
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="distribucion" class="col-form-label">Distribución de Calculo</label> <span class="required" style="color:red;"> * </span> 
                                                        <div class="input-group mb-3">                                        
                                                            <select class="required form-control" id="distribucion" name="distribucion" required onchange="distribuir();">
                                                                <option value="1">Automatica</option>
                                                                <option value="2">Manual</option>
                                                            </select>                                          
                                                        </div>
                                                    </div> 
                                                </div>
                                                <!---- pega contenido al agregar categorias --->
                                                <div id="pastecontent" class="col-md-12"> </div> 
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="add-category" class="col-form-label">&nbsp;</label> 
                                            <button type="button" class="btn btn-secondary btn-block" id="add-parameter" onclick="add_parameter();" 
                                                    data-conteo-par="0" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." >
                                                <i class="fas fa-file-medical"></i> &nbsp;Agregar Calificación por Parámetro</button>                                                    
                                        </div>
                                        <!---- pega contenido al agregar parametros --->
                                        <div id="pastecontent_parameters" class="col-md-12"> </div> 
                                        <br />                                       
                                        <div class="col-md-12">
                                            <div class="btn-group btn-block float-right">
                                                <button type="button" class="btn btn-info" onclick="stepper.previous()" data-toggle="tooltip" data-placement="top" title="Anterior"> <i class=" fa fa-arrow-left"></i></button>
                                                <button type="button" class="btn btn-primary" id="nextparametro"  onclick="stepper.next()" data-toggle="tooltip" data-placement="top" disabled title="Siguiente"> <i class=" fa fa-arrow-right"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="range-part" class="content" role="tabpanel" aria-labelledby="range-part-trigger">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="add-rang" class="col-form-label">&nbsp;</label> 
                                            <button type="button" class="btn btn-info btn-block" 
                                                    id="add-range" onclick="add_range();" 
                                                    data-conteo-range="0" 
                                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." >
                                                <i class="fas fa-plus-circle"></i> Agregar Rango de Descripcion de Conclusiones</button>                                                                                                        
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="valor_max" class="col-form-label">Valor maximo de la evaluación en %</label> <span class="required" style="color:red;"> * </span>           
                                                <div class="input-group">     
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-percent"></i></span>
                                                    </div>                                   
                                                    <input type="number" step="0.001" min="0.00" placeholder="0.000" required
                                                           class="form-control valor-base" id="valor_max3" name="valor_max3" disabled>
                                                </div>  
                                            </div>                                                                                                   
                                        </div>
                                        <!---- pega contenido al agregar parametros --->
                                        <div id="pastecontent_range" class="col-md-12"> </div> 
                                        <br />   
                                        <div class="col-md-12">
                                            <div class="btn-group btn-block float-right">
                                                <button type="button" class="btn btn-info" onclick="stepper.previous()" data-toggle="tooltip" data-placement="top" title="Anterior"> <i class=" fa fa-arrow-left"></i></button>
                                                <button type="submit" class="btn btn-primary" id="guardar" data-toggle="tooltip" data-placement="top" title="Registrar nueva evaluacion"> <i class="fa fa-save"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>                           
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
        </div>
    </section> 
    <!---CONTENIDO AL CREAR CATEGORIAS SE COPIA SE CAMBIA LOS INPUTS Y SE PEGA EN EL DOM -->
    <div id="copycontent" style="display:none;">
        <div class="row category">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="label1" class="col-form-label">Tipo</label> <span class="required" style="color:red;"> * </span>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                        </div>
                        <input name="label1" id="label1" class="form-control" required placeholder="Nombre de la Categoria">
                    </div>  
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="label2" class="col-form-label">Valor</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                        </div>                                          
                        <input type="number" step="0.001" min="0.00" placeholder="0.000" required 
                               class="form-control valor-categoria" oninput="sumar();" id="label2" name="label2">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="labeld" class="col-form-label">Descripción</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">                                        
                        <input type="text" class="form-control" id="labeld" name="labeld" rows="3" required placeholder="Describa la categoria...">                                      
                    </div>
                </div> 
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label for="deleted" class="col-form-label">&nbsp;</label> 
                    <div class="input-group">                                      
                        <button type="button" class="btn btn-danger btn-block delete" id="deleted" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Eliminar Esta Categoria"><i class="fas fa-minus-circle"></i></button>                                                    
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <!---CONTENIDO AL CREAR PARAMETROS SE COPIA SE CAMBIA LOS INPUTS Y SE PEGA EN EL DOM -->
    <div id="copycontent_parameter" style="display:none;">
        <div class="row parameter">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="label3" class="col-form-label">Nombre Parametro</label> <span class="required" style="color:red;"> * </span>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-file-contract"></i></span>
                        </div>
                        <input name="label3" id="label3" required class="form-control" placeholder="Nombre del Parámetro">
                    </div>  
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="label4" class="col-form-label">Valor 1</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                        </div>                                          
                        <input type="number" step="0.01" min="0.00" placeholder="0.00" required class="form-control valor-parametro1" id="label4" name="label4">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="label5" class="col-form-label">Valor 2</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                        </div>                                          
                        <input type="number" step="0.01" min="0.00" placeholder="0.00" required class="form-control valor-parametro2" id="label5" name="label5">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="labelcolor" class="col-form-label">Color</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-palette"></i></span>
                        </div>                                          
                        <input type="color" required class="form-control valor-color" id="labelcolor" name="labelcolor">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label for="" class="col-form-label">&nbsp;</label> 
                    <div class="input-group mb-3">                                      
                        <button type="button" class="btn btn-warning btn-block delete" id="deleted_parameter" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Eliminar Este Parámetro"><i class="fas fa-trash"></i></button>                                                    
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <!---CONTENIDO AL CREAR RANGOS SE COPIA SE CAMBIA LOS INPUTS Y SE PEGA EN EL DOM -->
    <div id="copycontent_range" style="display:none;">
        <div class="row range">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="label6" class="col-form-label">Descripción</label> <span class="required" style="color:red;"> * </span>
                    <div class="input-group">
                        <input type="text" class="required form-control" id="label6" name="label6" rows="3" 
                               placeholder="Describa la Conclusion De la Nota Final" required>
                    </div>  
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="label7" class="col-form-label">Valor Rango 1</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                        </div>                                          
                        <input type="number" step="0.01" min="0.00" placeholder="0.00" required class="required form-control valor-rango1" id="label7" name="label7">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="label8" class="col-form-label">Valor Rango 2</label> <span class="required" style="color:red;"> * </span> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                        </div>                                          
                        <input type="number" step="0.01" min="0.00" placeholder="0.00" required class="required form-control valor-rango2" id="label8" name="label8">                                           
                    </div>
                </div> 
            </div>
            <div class="col-md-1">
                <div class="form-group">
                    <label for="" class="col-form-label">&nbsp;</label> 
                    <div class="input-group mb-3">                                      
                        <button type="button" class="btn btn-danger btn-block delete" id="deleted_range" 
                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" 
                            data-placement="top" title="Eliminar Este Rango"><i class="fas fa-trash"></i></button>                                                    
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bs-stepper/css/bs-stepper.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">  
@endpush
@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/moment/moment.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/inputmask/jquery.inputmask.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bs-stepper/js/bs-stepper.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}" type="text/javascript"></script>
<script src="{{asset('js/habilidades/plugins.js')}}"></script>
@endpush