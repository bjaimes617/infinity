<input type="hidden" name="urlobservaciones" id="urlobservaciones" value="{{route('cursos.aprobacion.create')}}">
<input type="hidden" name="typevalidador" id="typevalidador" value="preactive">
<form action="{{route('cursos.lp.store')}}" method="post" id="formaprobacionparticipantes" class="form-horizontal  form-label-left">
    @CSRF
    @method('PUT')
    <br/>
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
                        <th>Accion</th>     
                        <th>Documentación</th>  
                        <th>Estatus para LP</th>      

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
                        <td><div class="form-group">                                                                    
                                <div class="input-group">                                              
                                    <select id="accion{{$infos["id"]}}" onchange="ChangeAccion({{$infos["id"]}})" required name="accion{{$infos["id"]}}" class="countaction select2" 
                                            style="width: 100%;">
                                        <option value="">[SELECCIONE]</option>
                                        @foreach($accion as $keya => $acc)
                                        <option value="{{$keya}}">{{$acc}}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                        </td>                        
                        <td><div class="form-group">                                                                    
                                <div class="input-group">                                              
                                    <select id="documentacion{{$infos["id"]}}" name="documentacion{{$infos["id"]}}" class="select2" 
                                            style="width: 100%;">
                                        <option value="">[SELECCIONE]</option>
                                        @foreach($documentacion as $key => $doc)
                                        <option value="{{$key}}">{{$doc}}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                        </td>
                        
                        <td><div class="form-group">                                                                    
                                <div class="input-group">                                              
                                    <select id="listado{{$infos["id"]}}" name="listado{{$infos["id"]}}" class="select2" 
                                            style="width: 100%;">
                                        <option value="">[SELECCIONE]</option>
                                        @foreach($estatus as $estatu)
                                        <option value="{{$estatu->id}}">{{$estatu->estatus}}</option>
                                        @endforeach
                                    </select> 
                                    <input type="hidden" id="participante" name="participante[]" value="{{$infos["id"]}}">
                                </div>
                            </div>
                        </td>                    
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class=" col-md-12 text-center">
        <button type="submit" id="sendmigracion" class="btn btn-lg btn-primary btn-block"  data-toggle="tooltip" data-placement="top" title="Procesar"> <i class=" fa fa-save"></i></button>   
    </div>
</form>