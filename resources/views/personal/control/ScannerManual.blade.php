@extends('layouts.scannerManual')
@section('title', 'Control de Asistencia Manual')
@section('content')
<div class="sidebar">
    <section class="cameras">
        <ul class="row">
            <div class="col-md-4">                                    
                <div class="form-group">
                    <label class="col-form-label" for="cedula_empleado" style="color:white;">Cedula de Identidad <span style="color:red">*</span></label>                                                                                                                                      
                    <div class="input-group">  
                        <input type="text" class="form-control" autocomplete="off" name="cedula_empleado" id="cedula_empleado"  
                            required value="{{old('cedula_empleado')}}" placeholder="Cedula de Identidad">                                                         
                        <button type="button" id="buscarpersonal" class="btn btn-primary btn-md" data-toggle="tooltip" data-placement="top" title="Buscar/Registrar"><i class="fa fa-search"></i></button>
                    </div>
                    
                </div>
            </div> 
            <!--<div class="col-sm-2"> 
              <div class="form-group">
                    <label class="col-form-label" for="temperatura">Temperatura <span style="color:red">*</span></label>                                                                                                                                      
                    <div class="input-group mb-2">  
                        <input type="text" class="form-control" autocomplete="off" name="temperatura" id="temperatura"  
                               required value="{{old('temperatura')}}" placeholder="Temperatura">                                                         
                    </div>                   
                </div>                
            </div>-->       
            <div class='col-md-8'>
                <img src="{{ asset('img/LOGO DG DIAPO.png') }}" style="float: right ;margin-right: -10px; margin-top:0px; margin-bottom: 0px; width: auto;"/>
            </div>
        </ul>
        
    </section>
    <!--<section class="scans">
      <h2>Scans</h2>
      <ul v-if="scans.length === 0">
        <li class="empty">No scans yet</li>
      </ul>
      <transition-group name="scans" tag="ul">
        <li v-for="scan in scans" :key="scan.date" :title="scan.content">@{{ scan.content }}</li>
      </transition-group>
    </section>-->
</div>
<div class="preview-container"> 

</div>
<iframe src="" id="data-container" frameborder="0"></iframe>  
<input type="hidden" id="sede" value="{{ $sede }}"/>

@endsection

@push('script')
<script type="text/javascript">

    $("#buscarpersonal").click(function () {        
        if ($("#cedula_empleado").val() != "" && $("#temperatura").val() != "") {
            var cedula = $("#cedula_empleado").val();
            var sede = $("#sede").val();
            var temperatura = $("#temperatura").val();
            
            var url = "{!!route('control.scanner.check')!!}" + "/" + cedula + "/" + sede + "/";
            window.open(url, "ventana1", "width=1000,height=600,scrollbars=NO");
            $("#cedula_empleado").val('');
            $("#temperatura").val('');
        } else {
            var Toastr = main();
            $(function () {
                Toastr.fire({
                    icon: 'error',
                    title: "&nbsp; Los campos de cedula y temperatura son obligatorios.",
                });
            });
        }
    });
    
    $("#cedula_empleado").keypress(function(e) {
         var code = (e.keyCode ? e.keyCode : e.which); 
         if(code == 13){ 
          if ($("#cedula_empleado").val() != "") {
            var cedula = $("#cedula_empleado").val();
            var sede = $("#sede").val();
            var temperatura = $("#temperatura").val();
            var url = "{!!route('control.scanner.check')!!}" + "/" + cedula + "/" + sede + "/";
            window.open(url, "ventana1", "width=1000,height=600,scrollbars=NO");
            $("#cedula_empleado").val('');
            $("#temperatura").val('');
        } else {
            var Toastr = main();
            $(function () {
                Toastr.fire({
                    icon: 'error',
                    title: "&nbsp; Los campos de cedula y temperatura son obligatorios.",
                });
            });
        }
        }      
    });
       
    $('.numero').keypress(function (event) {

        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

</script>
@endpush
