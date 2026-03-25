<input type="hidden" name="urlobservaciones" id="urlobservaciones" value="{{route('cursos.aprobacion.create')}}">
<input type="hidden" name="typevalidador" id="typevalidador" value="aprobar">
<form action="{{route('cursos.aprobacion.store',$curso)}}" method="post" id="formaprobacionparticipantes" class="form-horizontal  form-label-left">
    @CSRF
    @method('PUT')
    <div class="row mb-3">
        <div class="col-md-12">
            <table id="datatable-aprobacion" class="table compact table-sm text-sm table-bordered table-striped text-center">
                <thead>
                    <tr>        
                        <th>Cedula</th>
                        <th>Nombre y Apellido</th>
                        <th>Nro. asistencia</th>
                        <th>Nro. Inasistencias</th>  
                        <th>Estatus actual</th>      
                        <th>Estatus final</th>      
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($info as $infos)                
                <tr>                    
                    <td>{{$infos["cedula_empleado"]}}</td>
                    <td>{{$infos["nombre"]}}</td>
                    <td>{{$infos["asistencia"]}}</td>                        
                    <td>{{$infos["inasistencia"]}}</td>
                    <td>{{$infos["estatus"]}}</td>
                    <td>
                        <div class="form-group">                                                                    
                            <div class="input-group">                                              
                                <select id="listado{{$infos["id"]}}" name="listado{{$infos["id"]}}" required class="select2" 
                                        onchange="AprobacionesObservacionesChange({{$infos["id"]}});" style="width: 100%;">
                                    <option value="">[SELECCIONE]</option>
                                    @foreach($estatus as $estatu)
                                    <option value="{{$estatu->id}}">{{$estatu->estatus}}</option>
                                    @endforeach
                                </select> 
                                <input type="hidden" id="participante" name="participante[]" value="{{$infos["id"]}}">
                            </div>
                        </div>
                    </td>
                    <td><div class="form-group">                                                                    
                            <div class="input-group">                                              
                                <select id="observacion{{$infos["id"]}}" name="observacion{{$infos["id"]}}" required class="select2" 
                                        style="width: 100%;">
                                    <option value="">[SELECCIONE ESTATUS]</option>                                   
                                </select> 
                            </div>
                        </div></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class=" col-md-12 text-center">
    <button type="submit" class="btn btn-lg btn-primary btn-block"  data-toggle="tooltip" data-placement="top" title="Procesar"> <i class=" fa fa-save"></i></button>   
</div>
</form>