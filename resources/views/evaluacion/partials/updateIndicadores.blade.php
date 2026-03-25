<div class="tab-pane p-3 active preview" role="tabpanel" id="preview-912">
    <div class="table-responsive">
        <table id="indicador" class="table compact table-sm text-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th>Indicador</th>
                    <th>Meta</th>
                    <th>Puntaje <span style="color:red">*</span></th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($indicadores as $indicador)                                                       
                <input type="hidden" name="indicador_id[]" id="indicador_id" value="{{$indicador->id}}">                                                         
                <input type="hidden" name="meta[]" id="meta" value="{{$indicador->meta}}">
            <tr>
                <td class="text-center">{{$indicador->indicador}}</td>
                <td class="text-center">{{$indicador->meta}} @if($indicador->tipo == 1) % @endif</td>
                <td class="form-group">                                                               
                    <div class="input-group"> 
                        <input type="number" required name="puntaje[]" id="puntaje" value="{{$indicador->puntaje}}" 
                               class="form-control" step=".01" placeholder="0.00" min="0.00"
                               @if($indicador->tipo == 1) max="100" @endif 
                        autocomplete="off">
                    </div>                                                                
                </td>
            </tr>
            @php $i++; @endphp
            @endforeach
        </tbody>
        </table>
        <input type="hidden" name="count" id="count" value="{{$indicador_count}}">
        <input type="hidden" name="evaluacionid" id="evaluacionid" value="{{$evaluacion_id}}">        
    </div>                                                
</div>