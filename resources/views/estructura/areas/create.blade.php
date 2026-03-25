@extends('layouts.main')
@section('title', 'Estructura|Gerencia|Agregar')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">Estrctura</li>
                            <li class="breadcrumb-item active">Gerencias</li>
                            <li class="breadcrumb-item active">Agregar</li>
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
                                <h3 class="card-title">Agregar Estructura de Nuevas Gerencias, Nuevas Unidades o nuevos
                                    Skills</h3>
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
                                            <a href="#" onclick="displays('divgerencias')"
                                                class="small-box-footer">Agregar Nueva Gerencia <i
                                                    class="fas fa-arrow-circle-right"></i></a>
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
                                            <a href="#" onclick="displays('divunidades')"
                                                class="small-box-footer">Agregar Nuevos Departamentos/Campañas <i
                                                    class="fas fa-arrow-circle-right"></i></a>
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
                                            <a href="#" onclick="displays('divskill')"
                                                class="small-box-footer">Agregar Nuevos Segmentos / Skills <i
                                                    class="fas fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 hidendivs" id="divgerencias" style="display:none;">
                                    <form action="{{ route('estructura.areas.store', 'gerencias') }}" method="POST"
                                        id="gerencias_create" class="form-horizontal form-label-left" data-parsley-validate>
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label" for="area_trabajo">Nombre de la Gerencia<span
                                                        style="color:red">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" name="area_trabajo" id="area_trabajo" required
                                                        class="form-control" value="{{ old('area_trbajo') }}"
                                                        placeholder="Gerencia de XXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <button type="submit"class="btn btn-lg btn-primary btn-block"
                                                data-toggle="tooltip" data-placement="top" title="Guardar"> <i
                                                    class=" fa fa-save"></i></button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12 hidendivs" id="divunidades" style="display:none;">
                                    <form action="{{ route('estructura.areas.store', 'unidades') }}" method="POST"
                                        id="unidades_create" class="form-horizontal form-label-left" data-parsley-validate>
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="gerencia">Gerencia <span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <select name="gerencia" required id="gerencia"
                                                            class="form-control">
                                                            <option value="">[Seleccione]</option>
                                                            @foreach ($areas as $area)
                                                                <option value="{{ $area->id }}">
                                                                    {{ $area->nombre_campana }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="cartera">Departamento / Campaña<span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="cartera" id="cartera" required
                                                            class="form-control" value="{{ old('cartera') }}"
                                                            placeholder="Gerencia de XXXXXX">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <button type="submit"class="btn btn-lg btn-primary btn-block"
                                                    data-toggle="tooltip" data-placement="top" title="Guardar"> <i
                                                        class=" fa fa-save"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-12 hidendivs" id="divskill" style="display:none;">
                                    <form action="{{ route('estructura.areas.store', 'skill') }}" method="POST"
                                        id="skill_create" class="form-horizontal form-label-left" data-parsley-validate>
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="area_trabajo">Gerencia <span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <select name="area_trabajo" required id="area_trabajo"
                                                            data-href="{{ route('selector.areas.estructuras') }}"
                                                            class="form-control">
                                                            <option value="">[Seleccione]</option>
                                                            @foreach ($areas as $area)
                                                                <option value="{{ $area->id }}">
                                                                    {{ $area->nombre_campana }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="carteras">Departamento/Campaña
                                                        <span style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <select name="carteras" required id="carteras"
                                                            class="form-control">
                                                            <option value="">[Seleccione]</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="skills">Segmento / Skill <span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="skills" id="skills" required
                                                            class="form-control" value="{{ old('skkils') }}"
                                                            placeholder="XXXXXXXXXX">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="codceco">COD. Centro de Costo
                                                        <span style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="codceco" id="codceco" required
                                                            maxlength="5" class="form-control"
                                                            value="{{ old('codceco') }}" placeholder="XXXXXXXXXX">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="nombceco">Centro de Costo <span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" name="nombceco" id="nombceco" required
                                                            maxlength="50" class="form-control"
                                                            value="{{ old('nombceco') }}" placeholder="XXXXXXXXXX">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="tipo_periodo">Periodo de Pago<span
                                                            style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <select id="tipo_periodo" name="tipo_periodo"
                                                            class="form-control" required>
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
                                                    <label class="col-form-label" for="tipo_periodo">Díass
                                                        Disponibles<span style="color:red">*</span></label>
                                                    <div class="input-group">
                                                        <select id="dias" name="dias[]"
                                                            class="form-control select2" multiple="multiple"
                                                            style="width: 100%;">
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
                                                            <div class="col-md-12">
                                                                <p><i class="far fa-caret-square-down"></i> Rango de la
                                                                    1ra. Quincena</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="desde1">Desde el
                                                                        dia <span style="color:red">*</span></label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                            id="desde1" name="desde1" min="1"
                                                                            max="31">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="hasta1">Desde el
                                                                        dia <span style="color:red">*</span></label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                            id="hasta1" name="hasta1" min="1"
                                                                            max="31">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <p><i class="far fa-caret-square-down"></i> Rango de la
                                                                    2da. Quincena</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="desde2">Desde el
                                                                        dia <span style="color:red">*</span></label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                            id="desde2" name="desde2" min="1"
                                                                            max="31">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="col-form-label" for="hasta2">Desde el
                                                                        dia <span style="color:red">*</span></label>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control"
                                                                            id="hasta2" name="hasta2" min="1"
                                                                            max="31">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-center"><br>
                                                <button type="submit"class="btn btn-lg btn-primary btn-block"
                                                    data-toggle="tooltip" data-placement="top" title="Guardar"> <i
                                                        class=" fa fa-save"></i></button>
                                            </div>
                                    </form>
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
    <link media="all" type="text/css" rel="stylesheet"
        href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet"
        href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet"
        href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/select2/js/select2.js') }}"></script>
    <script src="{{ asset('js/estructura/estructura.js') }}" type="text/javascript"></script>
@endpush
