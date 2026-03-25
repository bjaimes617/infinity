<div class="card card-primary card-outline">   
    <div class="card-body">      
        <div class="row mb-3">
            <div class="col-md-2">
                <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-personal" role="tab" aria-controls="vert-tabs-personal" aria-selected="true"><i class="fa fa-user"></i> Datos Personales</a>
                    <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false"><i class="fas fa-user-graduate"></i> Educación</a>
                    <a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false"><i class="fas fa-file-contract"></i> Ref. Personales</a>
                    <a class="nav-link" id="vert-tabs-settings-tab" data-toggle="pill" href="#vert-tabs-settings" role="tab" aria-controls="vert-tabs-settings" aria-selected="false"><i class="fas fa-file-contract"></i> Trabajos Anteriores</a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="tab-content" id="vert-tabs-tabContent">
                    <div class="tab-pane text-left fade show active" id="vert-tabs-personal" role="tabpanel" aria-labelledby="vert-tabs-personal-tab">
                        <div class="line"></div> 
                        <div class="row mb-3">                                                     
                            <div class="col-md-12">       
                                <p><i class="fas fa-user"></i> 1. Datos personales principales</p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class=" text-center circular--portrait">
                                        <label for="image">
                                            <img id="previewimg" @if($pros->photo !== null) src="{{$pros->photo}}" @else  src="{{asset('img/user.png')}}" @endif data-toggle="tooltip" data-placement="top" title="Fotografía fondo Blanco"> 
                                        </label>                                                                  
                                        <input type="file" id="image" name="image" onchange="loadFile(event)" />                                                                         
                                    </div>  
                                    <p class="text-center"><small>Al Cargar la fotografía la misma debera ser de tipo carnet con fondo blanco. </small></p>
                                </div>
                            </div>   
                            <div class="col-md-6">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="pnombre">Nombres <span style="color:red">*</span></label>                                                                                                                                      
                                    <div class="input-group mb-2">  
                                        <input type="text" class="form-control" autocomplete="off" name="pnombre" id="pnombre"  
                                               required value="{{strtoupper($pros->nombres)}}" placeholder="Nombres Completos">                                                         
                                    </div>  
                                </div>
                            </div>       
                            <div class="col-md-6">                                    
                                <div class="form-group">
                                    <label class="col-form-label" for="papellido">Apellidos <span style="color:red">*</span></label>                                                                                                                                      
                                    <div class="input-group mb-2">  
                                        <input type="text" class="form-control" autocomplete="off" name="papellido" id="papellido"  
                                               required value="{{strtoupper($pros->apellidos)}}" placeholder="Apellidos Completos">                                                         
                                    </div>  
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="cedula_empleado">Cedula de Identidad <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <div class="input-group-prepend">
                                            <select name="nacionalidad" id="nacionalidad"  required class="form-control">
                                                @foreach($nacionalidades as $key => $nacionalidad)
                                                <option value='{{$key}}' @if($pros->nacionalidad == $key) selected @endif> {{$nacionalidad}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="text" class="form-control input-number" maxlength="11" required name="cedula_empleado" 
                                               id="cedula_empleado" value="{{$pros->cedula}}" placeholder="123456789">                                           
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="nacimiento">Fecha de Nacimiento <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        </div>
                                        <input type="text" class="form-control dateter" autocomplete="off" name="nacimiento" id="nacimiento"  
                                               required value="{{date('d/m/Y',strtotime($pros->fecha_nacimiento))}}" placeholder="01/01/1900"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="edad">Edad <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">                                                                    
                                        <input type="text" class="form-control" disabled autocomplete="off" name="edad" id="edad"  
                                               value="{{strtoupper($pros->edad)}}"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="lugarnacimiento">Lugar de Nacimiento <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">                                                                    
                                        <input type="text" class="form-control" vautocomplete="off" name="lugarnacimiento" id="lugarnacimiento"  
                                               required value="{{strtoupper($pros->lugarnacimiento)}}"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="pais">Pais <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">                                                                    
                                        <input type="text" class="form-control" vautocomplete="off" name="pais" id="pais"  
                                               required value="{{strtoupper($pros->pais)}}"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">                                    
                                <div class="form-group text-center">
                                    <label class="col-form-label" for="sexo">Genero <span style="color:red">*</span></label>                                                        
                                    <div class="input-group justify-content-center">
                                        <div class="btn-group btn-group-toggle float-justify-center" data-toggle="buttons">
                                            <label class="input-group btn bg-primary">
                                                <input type="radio" required name="sexo" id="sexo" value="F" @if($pros->sexo =="F") checked @endif autocomplete="off"> Femenino
                                            </label>  
                                            <label class="input-group btn bg-primary">
                                                <input type="radio" name="sexo" id="sexom" value="M" @if($pros->sexo =="M") checked @endif autocomplete="off"> Masculino
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-12">
                                <p><i class="fas fa-user-friends"></i> 2. Información Social</p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="personasviven">¿Cuántas personas viven con usted?<span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <input type="number" name="personasviven" id="personasviven"  value="{{$pros->personasviven}}" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="personasacargo">¿Cuántas a su cargo? <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <input type="number" name="personasacargo" id="personasacargo" value="{{$pros->personasacargo}}" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="edadesacargo">Edad(es) de la persona(s) a Cargo <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="edadesacargo"  required id="edadesacargo" value="{{$pros->edadesacargo}}" autocomplete="off" class="conteoEdades form-control" placeholder="5,4,3,2,1">
                                        <div class="input-group-prepend"  
                                             data-toggle="tooltip" 
                                             data-placement="top" 
                                             title="Separe con Comas y de Mayor a menor las Edades de las personas a su cargo, en caso contrario coloque Cero '0' ">
                                            <div class="input-group-text"> <i class="fas fa-question"></i></div>
                                        </div>
                                    </div>

                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="edocivil">Estado Civil <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <select name="edocivil" id="edocivil"  required class="form-control select2">
                                            <option value=''> [SELECCIONE]</option>
                                            @foreach($edocivil as $key => $civil)
                                            <option value='{{$key}}' @if($pros->edocivil == strtoupper($key)) selected @endif> {{$civil}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>                                                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="conyuge">Apellidos y Nombres del Conyuge </label>                                 
                                    <div  class="input-group">                                                                    
                                        <input type="text" class="form-control" autocomplete="off" name="conyuge" id="conyuge"  
                                               value="{{strtoupper($pros->conyuge)}}"> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="nrodehijos">¿Nro. de Hijos? <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <input type="number" name="nrodehijos" id="nrodehijos" value="{{strtoupper($pros->hijos)}}" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="edadhijos">Edad(es) de sus hijos </label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="edadhijos" id="edadhijos" required value="{{strtoupper($pros->edadhijos)}}" autocomplete="off" class="conteoEdades form-control" placeholder="5,4,3,2,1">
                                        <div class="input-group-prepend"  
                                             data-toggle="tooltip" 
                                             data-placement="top" 
                                             title="Separe con Comas y de Mayor a Menor las Edad de cada hijo si aplica, en caso contrario coloque 0.">
                                            <div class="input-group-text"> <i class="fas fa-question"></i></div>
                                        </div>
                                    </div>

                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="sexohijos">Sexo de sus hijos </label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="sexohijos" id="sexohijos" value="{{strtoupper($pros->sexohijos)}}" autocomplete="off" class="conteoGeneros form-control" placeholder="M,F">
                                        <div class="input-group-prepend"  
                                             data-toggle="tooltip" 
                                             data-placement="top" 
                                             title=" Separe con Comas y de Mayor a Menor el Sexo de cada hijo, Indique (M) Para masculino o (F) para femenino">
                                            <div class="input-group-text"> <i class="fas fa-question"></i></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-12">
                                <p> <i class="fas fa-map-marker"></i> 3. Datos de Ubicación y Contacto</p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="direccion">Dirección de Habitación <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="direccion" id="direccion" required value="{{strtoupper($pros->direccion)}}" autocomplete="off" class="form-control" placeholder="Direccion de Habitacion">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="ciudad">Ciudad <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="ciudad" id="ciudad" required value="{{strtoupper($pros->ciudad)}}" autocomplete="off" class="form-control" placeholder="XXXXX">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="parroquia">Parroquía <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="parroquia" id="parroquia" required value="{{strtoupper($pros->parroquia)}}" autocomplete="off" class="form-control" placeholder="XXXXX">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="estado">Estado <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="estado" id="estado" required value="{{strtoupper($pros->estado)}}" autocomplete="off" class="form-control" placeholder="XXXXX">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="celular">Teléfono Celular <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="celular" id="celular" required value="{{strtoupper($pros->telefono_celular)}}" autocomplete="off" maxlength="10" class="form-control input-number" placeholder="4120000000">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="habitacion">Teléfono de Habitación <span style="color:red">*</span></label>                                 
                                    <div class="input-group">                                                                   
                                        <input type="text" name="habitacion" id="habitacion" required value="{{strtoupper($pros->telefono_casa)}}" autocomplete="off" maxlength="10" class="form-control input-number" placeholder="21200000000">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-form-label" for="email">E-mail <span style="color:red">*</span></label>                                 
                                    <div class="input-group">  
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                                        </div>
                                        <input type="email" name="email" id="email" required value="{{strtoupper($pros->email)}}" autocomplete="off" class="form-control" placeholder="example@example.com">                                                                    
                                    </div>                                                               
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p> <i class="fas fa-user-tag"></i> 4. Datos Adicionales </p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="camisa">Tallas de camisa </label>                                 
                                            <div class="input-group">                                                                   
                                                <input type="text" name="camisa" id="camisa" required value="{{strtoupper($pros->camisa)}}" autocomplete="off" maxlength="3" class="form-control" placeholder="S,M,L,Xl,2XL">                                                                    
                                            </div>                                                               
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="pantalon">Talla de Pantalon </label>                                 
                                            <div class="input-group">                                                                   
                                                <input type="text" name="pantalon" id="pantalon" required value="{{strtoupper($pros->pantalon)}}" autocomplete="off" maxlength="3" class="form-control" placeholder="Indique su talla de pantalon">                                                                    
                                            </div>                                                               
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-form-label" for="zapato">Talla de calzado </label>                                 
                                            <div class="input-group">                                                                   
                                                <input type="number" name="zapato" id="zapato" required value="{{strtoupper($pros->zapatos)}}" autocomplete="off" maxlength="3" class="form-control" placeholder="Indique su talla de calzado">                                                                    
                                            </div>                                                               
                                        </div>
                                    </div>
                                </div>                                                            
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-form-label" for="redes">Donde viste nuestra oferta de empleo <span style="color:red">*</span></label>                                 
                                    <div  class="input-group">
                                        <select name="redes" id="redes"  required class="form-control select2">
                                            <option value=''> [SELECCIONE]</option>
                                            @foreach($redes as $re => $red)
                                            <option value='{{$re}}' @if($pros->redes == strtoupper($re)) selected @endif> {{$red}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="trabajoantes">¿Trabajó antes en esta empresa? <span style="color:red">*</span></label>                                 
                                            <div  class="input-group">
                                                <select name="trabajoantes" id="trabajoantes"  required class="form-control select2">
                                                    <option value=''> [SELECCIONE]</option>
                                                    <option value="S"   @if($pros->trabajoantes == "S") selected @endif > SI</option>
                                                    <option value="N"   @if($pros->trabajoantes == "N") selected @endif  > NO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 fechaanterior" @if($pros->trabajoantes == "N")  style="display:none;" @endif >
                                        <div class="form-group">
                                            <label class="col-form-label" for="fechantes">¿En qué fecha? <span style="color:red">*</span></label>                                 
                                            <div  class="input-group">                                                                    
                                                <input type="text" class="dateter form-control" autocomplete="off" name="fechantes" id="fechantes" value="{{$pros->fechantes != null ? date('d/m/Y',strtotime($pros->fechantes)) : null }}"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label" for="parientes">¿Tiene parientes en esta empresa? <span style="color:red">*</span></label>                                 
                                            <div  class="input-group">
                                                <select name="parientes" id="parientes"  required class="form-control select2">
                                                    <option value=''> [SELECCIONE]</option>
                                                    <option value="S"   @if($pros->parientes == "S") selected @endif > SI</option>
                                                    <option value="N"   @if($pros->parientes == "N") selected @endif  > NO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 displaymencionelos" @if($pros->parientes == "N") style="display:none;" @endif >
                                        <div class="form-group">
                                            <label class="col-form-label" for="fechantes">Menciónelos <span style="color:red">*</span></label>                                 
                                            <div  class="input-group">                                                                    
                                                <input type="text" class="form-control" autocomplete="off" name="mencionelos" id="mencionelos" value="{{$pros->mencionelos != null ? strtoupper($pros->mencionelos) : null }}"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                        <div class="line"></div> 
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <br>
                                <p><i class="fas fa-user-graduate"></i> 5. Información Academica</p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-12 table-responsive p-0">
                                <table class="table compact table-sm table-head-fixed text-nowrap table-bordered table-striped text-center">
                                    <thead>
                                        <tr>                                            
                                            <th>Estudios</th>
                                            <th>Comienzo</th>
                                            <th>Culminación</th>
                                            <th>Años Cursados</th>                                    
                                            <th>Titulo Obt.</th> 
                                            <th>Universidad o Inst.</th> 
                                            <th>Lugar</th>                                                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>Bachillerato</b></div></td>
                                            <td><div class="form-group"><input type="text" required name="initbachiller" id="initbachiller" maxlength="4" class="form-control input-number" placeholder="XXXX"value="{{strtoupper($pros->initbachiller)}}"></div></td>
                                            <td><div class="form-group"><input type="text" required name="endbachiller" id="endbachiller" maxlength="4" class="form-control input-number" placeholder="XXXX" value="{{strtoupper($pros->endbachiller)}}"></div></td>
                                            <td><div class="form-group"><input type="number" required name="cursadobachiller" id="cursadobachiller" class="form-control" placeholder="Años Cursados" value="{{strtoupper($pros->cursadobachiller)}}"></div></td>
                                            <td><div class="form-group"><input type="text"  required name="titulobachiller" id="titulobachiller" class="form-control" placeholder="Titulo Obtenido" value="{{strtoupper($pros->titulobachiller)}}"></div></td>
                                            <td><div class="form-group"><input type="text" required name="instbachiller" id="instbachiller" class="form-control" placeholder="Donde curso el Bachillerato" value="{{strtoupper($pros->instbachiller)}}"></div></td>
                                            <td><div class="form-group"><input type="text" required name="lugarbachiller" id="lugarbachiller" class="form-control" placeholder="Ubicacion donde estudio" value="{{strtoupper($pros->lugarbachiller)}}"></div></td>
                                        </tr>
                                        <tr>
                                            <td><b>TSU</b></div></td>
                                            <td><div class="form-group"><input type="text"      name="inittsu" id="inittsu" maxlength="4" class="form-control input-number" placeholder="XXXX"value="{{strtoupper($pros->inittsu)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="endtsu"   id="endtsu" maxlength="4" class="form-control input-number" placeholder="XXXX" value="{{strtoupper($pros->endtsu)}}"></div></td>
                                            <td><div class="form-group"><input type="number"    name="cursatsu" id="cursadotsu" class="form-control" placeholder="Años Cursados" value="{{strtoupper($pros->cursadotsu)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="titulotsu"id="titulotsu" class="form-control" placeholder="Titulo Obtenido" value="{{strtoupper($pros->titulotsu)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="insttsu"  id="insttsu" class="form-control" placeholder="Donde curso" value="{{strtoupper($pros->insttsu)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="lugartsu" id="lugartsu" class="form-control" placeholder="Ubicacion donde estudio" value="{{strtoupper($pros->lugartsu)}}"></div></td>
                                        </tr>
                                        <tr>
                                            <td><b>Especialización</b></div></td>
                                            <td><div class="form-group"><input type="text"      name="initespecializacion" id="initespecializacion" maxlength="4" class="form-control input-number" placeholder="XXXX"value="{{strtoupper($pros->initespecializacion)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="endespecializacion"   id="endespecializacion" maxlength="4" class="form-control input-number" placeholder="XXXX" value="{{strtoupper($pros->endespecializacion)}}"></div></td>
                                            <td><div class="form-group"><input type="number"    name="cursaespecializacion" id="cursadoespecializacion" class="form-control" placeholder="Años Cursados" value="{{strtoupper($pros->cursadoespecializacion)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="tituloespecializacion"id="tituloespecializacion" class="form-control" placeholder="Titulo Obtenido" value="{{strtoupper($pros->tituloespecializacion)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="instespecializacion"  id="instespecializacion" class="form-control" placeholder="Donde curso" value="{{strtoupper($pros->instespecializacion)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="lugarespecializacion" id="lugarespecializacion" class="form-control" placeholder="Ubicacion donde estudio" value="{{strtoupper($pros->lugarespecializacion)}}"></div></td>
                                        </tr>
                                        <tr>
                                            <td><b>Postgrado</b></div></td>
                                            <td><div class="form-group"><input type="text"      name="initpostgrado" id="initpostgrado" maxlength="4" class="form-control input-number" placeholder="XXXX"value="{{strtoupper($pros->initpostgrado)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="endpostgrado"   id="endpostgrado" maxlength="4" class="form-control input-number" placeholder="XXXX" value="{{strtoupper($pros->endpostgrado)}}"></div></td>
                                            <td><div class="form-group"><input type="number"    name="cursapostgrado" id="cursadopostgrado" class="form-control" placeholder="Años Cursados" value="{{strtoupper($pros->cursadopostgrado)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="titulopostgrado"id="titulopostgrado" class="form-control" placeholder="Titulo Obtenido" value="{{strtoupper($pros->titulopostgrado)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="instpostgrado"  id="instpostgrado" class="form-control" placeholder="Donde curso " value="{{strtoupper($pros->instpostgrado)}}"></div></td>
                                            <td><div class="form-group"><input type="text"      name="lugarpostgrado" id="lugarpostgrado" class="form-control" placeholder="Ubicacion donde estudio" value="{{strtoupper($pros->lugarpostgrado)}}"></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>       
                            <div class="col-md-12">   
                                <div class="form-group">
                                    <label class="col-form-label" for="estudiosactuales">¿Qué Estudio o Curso sigue actualmente? </label>                                 
                                    <div  class="input-group">
                                        <div  class="input-group">                                                                    
                                            <input type="text" class="form-control" autocomplete="off" name="estudiosactuales" id="estudiosactuales" value="{{strtoupper($pros->estudiosactuales)}}"> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">   
                                <div class="form-group">
                                    <label class="col-form-label" for="estudiosactuales">Otros </label>                                 
                                    <div  class="input-group">
                                        <div  class="input-group">                                                                    
                                            <input type="text" class="form-control" autocomplete="off" name="otrosestudios" id="otrosestudios" value="{{strtoupper($pros->otrosestudios)}}"> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="vert-tabs-messages" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
                        <div class="line"></div> 
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <br>
                                <p><i class="fas fa-people-arrows"></i> 6. Referencias Personales (No Familiares)</p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-12 table-responsive p-0">
                                <table class="table compact table-sm table-head-fixed text-nowrap table-bordered table-striped text-center">
                                    <thead>
                                        <tr>                                            
                                            <th>Nombre y Apellido</th>
                                            <th>Telefonos</th>
                                            <th>Ocupación</th>
                                            <th>Cuanto tiempo conociéndose:</th>                                                                                                                                                                                    
                                        </tr>
                                    </thead>
                                    <tr>                                                                 
                                        <td><div class="form-group"><input type="text" required name="referencia1" id="referencia1" class="form-control" placeholder="XXXX" value="{{strtoupper($pros->referencia1)}}"></div></td>                                                                    
                                        <td><div class="form-group"><input type="text" required name="telefono1" id="telefono1" maxlength="10" class="form-control  input-number" placeholder="4120000000" value="{{strtoupper($pros->telefono1)}}"></div></td>
                                        <td><div class="form-group"><input type="text" required name="ocuacion1" id="ocuacion1" class="form-control" placeholder="Ocuacion" value="{{strtoupper($pros->ocuacion1)}}"></div></td>
                                        <td><div class="form-group"><input type="text" required name="refanios1" id="refanios1" maxlength="2" class="form-control input-number" placeholder="XXXX" value="{{strtoupper($pros->refanios1)}}"></div></td>
                                    </tr>
                                    <tr>                                                                 
                                        <td><div class="form-group"><input type="text" required name="referencia2" id="referencia2" class="form-control" placeholder="XXXX" value="{{strtoupper($pros->referencia2)}}"></div></td>                                                                    
                                        <td><div class="form-group"><input type="text" required name="telefono2" id="telefono2" maxlength="10" class="form-control  input-number" placeholder="4120000000" value="{{strtoupper($pros->telefono2)}}"></div></td>
                                        <td><div class="form-group"><input type="text" required name="ocuacion2" id="ocuacion2" class="form-control" placeholder="Ocuacion" value="{{strtoupper($pros->ocuacion2)}}"></div></td>
                                        <td><div class="form-group"><input type="text" required name="refanios2" id="refanios2" maxlength="2" class="form-control input-number" placeholder="XXXX" value="{{strtoupper($pros->refanios2)}}"></div></td>
                                    </tr>
                                </table>
                            </div>                                                                                                          
                        </div> 
                    </div>
                    <div class="tab-pane fade" id="vert-tabs-settings" role="tabpanel" aria-labelledby="vert-tabs-settings-tab">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <br>
                                <p><i class="fas fa-building"></i> 7. Información Laboral Anterior</p>
                                <div class="line"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5>1ra. Informacion Laboral anterior</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">                                                                    
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="nombreempresa1">Nombre de la Empresa </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="nombreempresa1" id="nombreempresa1" value="{{strtoupper($pros->nombreempresa1)}}" autocomplete="off" class="form-control" placeholder="Nombre de la Empresa">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="direccionempresa1">Dirección </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="direccionempresa1" id="direccionempresa1" value="{{strtoupper($pros->direccionempresa1)}}" autocomplete="off" class="form-control" placeholder="Dirección Completa de la empresa">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="ciudadempresa1">Ciudad </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="ciudadempresa1" id="ciudadempresa1" value="{{strtoupper($pros->ciudadempresa1)}}" autocomplete="off" class="form-control" placeholder="Dirección Completa de la empresa">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="telefonoempresa1">Telefono </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="telefonoempresa1" id="telefonoempresa1" maxlength="10" value="{{strtoupper($pros->telefonoempresa1)}}" autocomplete="off" class="form-control input-number" placeholder="2120000000">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="jefeinmediato1">Nombre del jefe inmediato </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="jefeinmediato1" id="jefeinmediato1"  value="{{strtoupper($pros->jefeinmediato1)}}" autocomplete="off" class="form-control" placeholder="Nombre y Apellido">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="cargo1">Cargo desempeñado </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="cargo1" id="cargo1" value="{{strtoupper($pros->ciudadempresa1)}}" autocomplete="off" class="form-control" placeholder="Cargo Desempeñado">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="acargo1">Personas a su cargo </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="number" name="acargo1" id="acargo1" value="{{strtoupper($pros->jefeinmediato1)}}" autocomplete="off" maxlength="3" class="form-control " placeholder="0">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fingreso1">Fecha de Ingreso </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control dateter" autocomplete="off" name="fingreso1" id="fingreso1"  
                                                               value="{{$pros->fingreso1 != null ? date('d/m/Y',strtotime($pros->fingreso1)) : ""}}" placeholder="01/01/1900"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fegreso1">Fecha de Egreso </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control dateter" autocomplete="off" name="fegreso1" id="fegreso1"  
                                                               value="{{$pros->fingreso1 != null ? date('d/m/Y',strtotime($pros->fingreso1)) : ""}}" placeholder="01/01/1900"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="tiempo1">Tiempo de Servicio </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="tiempo1" id="tiempo1"  
                                                               value="{{strtoupper($pros->tiempo1)}}" placeholder="Cantidad de años en la empresa"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="sueldo11">Sueldo Inicial </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="sueldo11" id="sueldo11"  
                                                               value="{{strtoupper($pros->sueldo11)}}" placeholder="Sueldo inicial percibido"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="sueldo12">Sueldo Final </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="sueldo12" id="sueldo12"  
                                                               value="{{strtoupper($pros->sueldo12)}}" placeholder="Sueldo final percibido"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="motivo1">Motivo de su Egreso </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="motivo1" id="motivo1"  
                                                               value="{{strtoupper($pros->motivo1)}}" placeholder="Exponga el motivo de su egreso"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="observaciones1">Observaciones </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="observaciones1" id="observaciones1"  
                                                               value="{{strtoupper($pros->observaciones1)}}" placeholder="observaciones"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h5>2da. Informacion Laboral anterior</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">                                                                    
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="nombreempresa2">Nombre de la Empresa </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="nombreempresa2" id="nombreempresa2" value="{{strtoupper($pros->nombreempresa2)}}" autocomplete="off" class="form-control" placeholder="Nombre de la Empresa">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="direccionempresa2">Dirección </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="direccionempresa2" id="direccionempresa2" value="{{strtoupper($pros->direccionempresa2)}}" autocomplete="off" class="form-control" placeholder="Dirección Completa de la empresa">      
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="ciudadempresa2">Ciudad </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="ciudadempresa2" id="ciudadempresa2" value="{{strtoupper($pros->ciudadempresa2)}}" autocomplete="off" class="form-control" placeholder="Dirección Completa de la empresa">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="telefonoempresa2">Telefono </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="telefonoempresa2" id="telefonoempresa2" maxlength="10" value="{{strtoupper($pros->telefonoempresa2)}}" autocomplete="off" class="form-control input-number" placeholder="2120000000">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="jefeinmediato2">Nombre del jefe inmediato </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="jefeinmediato2" id="jefeinmediato2"  value="{{strtoupper($pros->jefeinmediato2)}}" autocomplete="off" class="form-control" placeholder="Nombre y Apellido">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="cargo2">Cargo desempeñado </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" name="cargo2" id="cargo2" value="{{strtoupper($pros->cargo2)}}" autocomplete="off" class="form-control" placeholder="Cargo Desempeñado">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="acargo2">Personas a su cargo </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="number" name="acargo2" id="acargo2" value="{{strtoupper($pros->acargo2)}}" autocomplete="off" maxlength="2" class="form-control " placeholder="0">                                                                    
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fingreso2">Fecha de Ingreso </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control dateter" autocomplete="off" name="fingreso2" id="fingreso2"  
                                                               value="{{$pros->fingreso2 != null ? date('d/m/Y',strtotime($pros->fingreso2)) : ""}}" placeholder="01/01/1900"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="fegreso2">Fecha de Egreso </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control dateter" autocomplete="off" name="fegreso2" id="fegreso2"  
                                                               value="{{$pros->fegreso2 != null ? date('d/m/Y',strtotime($pros->fegreso2)) : ""}}" placeholder="01/01/1900"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="tiempo2">Tiempo de Servicio </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="tiempo2" id="tiempo2"  
                                                               value="{{strtoupper($pros->tiempo2)}}" placeholder="Cantidad de años en la empresa"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="sueldo21">Sueldo Inicial </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="sueldo21" id="sueldo21"  
                                                               value="{{strtoupper($pros->sueldo21)}}" placeholder="Sueldo inicial percibido"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="sueldo22">Sueldo Final </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="sueldo22" id="sueldo22"  
                                                               value="{{strtoupper($pros->sueldo22)}}" placeholder="Sueldo final percibido"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="motivo1">Motivo de su Egreso </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="motivo1" id="motivo1"  
                                                               value="{{strtoupper($pros->motivo1)}}" placeholder="Exponga el motivo de su egreso"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="col-form-label" for="observaciones2">Observaciones </label>                                 
                                                    <div class="input-group">                                                                   
                                                        <input type="text" class="form-control" autocomplete="off" name="observaciones2" id="observaciones2"  
                                                               value="{{strtoupper($pros->observaciones2)}}" placeholder="observaciones"> 
                                                    </div>                                                               
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <!-- /.card -->
</div>
