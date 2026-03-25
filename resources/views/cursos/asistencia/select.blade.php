<form action="{{route('cursos.asistencia.store')}}" method="POST" id="asistenciastore" class="form-horizontal  form-label-left">
    @CSRF    
    <div class="row mb-3">
        <div class="col-md-12" style="text-align: right;">                                            
            <button type="button" onclick="checkAsistencia()" class="btn btn-primary btn-flat" data-toggle="tooltip" data-placement="top" title="Selecionar Todos">
                <i class="fas fa-check-circle"></i>
            </button>                                     
        </div> 
        <input type="hidden" name="curso" id="curso" value="{{$curso->id}}">       
        <input type="hidden" name="fincurso" id="fincurso" value="{{$curso->finaliza}}"> 
        <input type="hidden" name="fechaasistencia" id="fechaasistencia" value="{{$fechas}}">
        @foreach($personalacargo as $acargo)
        <input type="hidden" name="operador[]" id="operador[]" value="{{$acargo->id}}">        
        <div class="col-md-12">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">                                        
                        <label class="col-form-label" for="listado{{$acargo->id}}">{{$acargo->primer_nombre}} {{$acargo->primer_apellido}} <span style="color:red">*</span></label>  
                        <div class="input-group">                                              
                            <select id="listado{{$acargo->id}}" name="listado{{$acargo->id}}" required class="select2 inputasistencia" data-valor="{{$acargo->id}}" style="width: 100%;">
                                <option value="">{{$acargo->cedula_empleado}}</option>
                                @foreach($items as $key => $itemasisten)
                                <option value="{{$key}}">{{$itemasisten}}</option>
                                @endforeach
                            </select>                                                   
                        </div> 
                    </div>                                       
                </div>                
                <div class="col-md-6">
                    <div class="form-group">                                        
                        <label class="col-form-label" for="listado{{$acargo->id}}">Observaciones para {{$acargo->primer_nombre}} {{$acargo->primer_apellido}} <span style="color:red">*</span></label>  
                        <div class="input-group">                                              
                            <input name="observaciones{{$acargo->id}}" id="observaciones{{$acargo->id}}" class="form-control" value="N/A">                                                
                        </div> 
                    </div>                                       
                </div>
                <div class="col-md-12 motivos" style="display:none;" id="display{{$acargo->id}}">
                    <div class="form-group">                                        
                        <label class="col-form-label" for="motivo{{$acargo->id}}">Motivo del Egreso<span style="color:red">*</span></label>  
                        <div class="input-group">    
                            <select id="motivo{{$acargo->id}}" name="motivo{{$acargo->id}}" required class="select2" style="width: 100%;">
                                <option value="">[Seleccione]</option>                                
                                <option value="0">N/A</option>                              
                            </select>                            
                        </div> 
                    </div>   
                </div>
            </div>            
            <hr>
        </div>
        @endforeach
    </div>
</div>
<div class="card-footer text-center">
    <button type="submit" class="btn btn-lg btn-primary"  data-toggle="tooltip" data-placement="top" title="Guardar"> <i class=" fa fa-save"></i></button>   
</div>
</form>
<!--nos traemos los motivos de egresos y abandonos desde el controlador y por Jquery cambiamos entre arreglos-->
<script>
    var egresos = {!!$egresos!!}
    ;
    var abandonos = {!!$abandonos!!}
    ;
</script>