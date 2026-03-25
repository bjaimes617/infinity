<div class="card-body">  
    @csrf     
    <div class="row mb-3">
        <div class="col-md-12" style="text-align: right;">                                                      
            <div class="btn-group">  
                <button type="button" onclick="checkAsistencia();" class="btn btn-primary btn-flat" data-toggle="tooltip" data-placement="top" title="Selecionar Todos">
                    <i class="fas fa-check-circle"></i>
                </button>                                            
            </div> 
        </div>
        <input type="hidden" name="tipo" id="tipo" value="{{$tipo}}">        
        <input type="hidden" name="fechaasistencia" id="fechaasistencia">  
        <div class="col-md-12" id="tablelistado" >   
            <hr>
            <table id="datatable-asistencias" class="table compact table-sm text-sm table-bordered text-center">
                <thead>
                    <tr>                                            
                        <th>Datos del Personal</th>
                        <th>Prefijo Asistencia</th>
                        <th>Observaciones</th>                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($personalacargo as $acargo)                        
                    <tr>
                        <td>
                            <b> {{$acargo->primer_nombre.' '.$acargo->primer_apellido}}</b>
                            <br/>
                            <b>1er. Lider: </b>{{$acargo->RelationprimerLider->primer_nombre." ".$acargo->RelationprimerLider->primer_apellido}} 
                            <br/> <b> 2do. Lider:</b> {{$acargo->RelationsegundoLider != null ? $acargo->RelationsegundoLider->primer_nombre." ".$acargo->RelationsegundoLider->primer_apellido: "N/D"}}
                            <br/> <b> Skill:</b> {{$acargo->Relationskill != null ? $acargo->Relationskill->nombre_skill: "N/D"}}
                            <input type="hidden" name="operador[]" id="operador[]" value="{{$acargo->id}}">
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="col-form-label" for="{{$acargo->id}}"></label>                                 
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <select name="{{$acargo->id}}" id="{{$acargo->id}}" required class=" select2 inputasistencia">                                                   
                                        <option value="">{{$acargo->RelationCargos->nombre_cargo}}</option>
                                     @foreach($items as $key => $values)
                                     <option value="{{$key}}">{{$values}}</option>
                                     @endforeach
                                    </select>                                           
                                </div> 
                            </div> 
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="col-form-label" for="observaciones{{$acargo->id}}"></label>                                 
                                <div  class="input-group">                    
                                    <input type="text" class="form-control" name="observaciones{{$acargo->id}}" id="observaciones{{$acargo->id}}"  
                                           required value="N/A"> 
                                </div>
                            </div> 
                        </td>
                    </tr>
                    @endforeach      
                </tbody>
            </table>                                
        </div>

    </div>
    <div class="col-md-12">
        <div class="form-group">                                      
            <div class="input-group">                                      
                <button type="submit" class="btn btn-primary btn-block" id="busquedalp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Procesando..." data-toggle="tooltip" data-placement="top" title="Registrar Asistencia"><i class="fa fa-save"></i></button>                                                    
            </div> 
        </div>
    </div>
</div>
