@extends('layouts.main')
@section('title', 'Usuarios del Sistema')
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
                        <li class="breadcrumb-item">Seguridad</li>
                        <li class="breadcrumb-item active">Usuarios</li>
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
                            <h3 class="card-title">Listado de usuarios</h3>
                            <div class="btn-group  float-right">
                                <button type='button' class="btn btn-info" data-toggle="modal" data-toggle="tooltip" data-target="#modal-default"  data-placement="top" 
                                        title="Crear Llave de Acceso"><i class="fa fa-key"></i></button>
                                <a href="{{route('users.create')}}" class="btn btn-primary" id="add" data-toggle="tooltip" data-placement="top" title="Añadir"><i class="fa fa-plus-circle"></i></a>
                            </div>
                        </div>
                        <input type="hidden" id="searchPermissionsUsers" value="{{route('users.permissions.search')}}">
                        <input type="hidden" id="searchTokensUsers" value="{{route('users.tokens.show')}}">
                        
                        <!-- /.card-header  modal-tokens-->
                        <div class="card-body">
                            <p><small>{{trans('mensajeTabla')}}</small></p>
                            <table id="usuarios_table" class="table compact table-sm table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Nombre y Apellido</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Estatus</th>
                                        <th>Creado</th>     
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->username}}</td>
                                        <td>{{$user->email}}</td>
                                        <td> @switch( $user->estatus_id)
                                            @case("Nuevo")
                                            <span class="badge badge-primary">{{ strtoupper($user->estatus_id)}}</span>
                                            @break
                                            @case("Inactivo")
                                            <span class="badge badge-danger">{{strtoupper($user->estatus_id)}}</span>
                                            @break
                                            @default
                                            <span class="badge badge-success">{{ strtoupper($user->estatus_id)}}</span>
                                            @break
                                            @endswitch
                                        </td>
                                        <td>{{date('d/m/Y',strtotime($user->created_at))}}</td>                                         
                                        <td>                                             
                                            @permission('edit.users | remove.users')
                                            <div class="btn-group">
                                                <button type='button'
                                                        class='btn btn-success btn-sm' 
                                                        onclick="ModalPermissions({{$user->id}});" 
                                                        ><i class="fas fa-eye"></i></button>
                                                
                                                <button type='button' {{!$user->tokens()->exists() ? 'disabled': ''}} 
                                                class='btn btn-info btn-sm' data-placement="top" 
                                                title="Listado de tokens" 
                                                onclick="ModelTokens({{$user->id}});"><i class="fas fa-key"></i></button>

                                                @permission('edit.users')
                                                <a href="{{route('users.edit',$user->id)}}" target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a> 
                                                @endpermission
                                                @permission('remove.users')
                                                <button type="button" onclick="Destroy({{$user->id}});" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>                        
                                                @endpermission
                                            </div>
                                            @endpermission
                                            <form  action="{{route('users.remove',$user->id)}}" method="POST" id="eliminar{{$user->id}}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>                                                                              
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- MODAL DE PERMISOS-->
                            <div class="modal fade" id="modal-permisos">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Permisologia adicional al rol principal</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12"><h4> Rol Principal del usuario: <b><span id="RolPrincipal" style="color:red;"></span></b></h4></div>
                                                <br>
                                                <hr>
                                                <div class="col-md-12">                                                  
                                                    <table id="PermissionsUsers" class="table table-sm compact table-bordered table-striped text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>Nombre del permiso</th>
                                                                <th>Slug</th>                                                                                                                                                                                     
                                                            </tr>
                                                        </thead>
                                                        <tbody>                                                       
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>                                          
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- MODAL AGREGAR TOKENS--> 
                            <div class="modal fade" id="modal-default">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Creacion de Tokens de Acceso</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{route('users.tokens.store')}}" method="post" id="form-tokens" >
                                            @csrf   
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="col-form-label" for="nombre">Nombre del Token <span style="color:red">*</span></label>                                 
                                                            <div  class="input-group ">
                                                                <input type="text" name="nombre" id="nombre" required class="form-control"  value="{{old('nombre')}}" placeholder="Nombre del Token">                                           
                                                            </div>
                                                        </div>  
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">                                   
                                                            <label class="col-form-label" for="usuario">Usuario del Sistema <span style="color:red">*</span></label>                               
                                                            <div  class="input-group">
                                                                <select name="usuario" id="usuario" required data-allow-clear="true" multiple class="form-control select2" style="width: 100%;" >                                                   
                                                                    <option value="">[Seleccione]</option>                                                              
                                                                    @foreach($users as $keys => $user)
                                                                    <option value="{{$user->id}}">{{$user->name}} : {{$user->username}}</option>                                           
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">                                   
                                                            <label class="col-form-label" for="permisos">Permisos/Habilidades del Acceso</label>                                 
                                                            <div  class="input-group">
                                                                <select name="permisos[]" id="permisos" data-allow-clear="true" required multiple class="form-control select2" style="width: 100%;" >                                                   
                                                                    <option value="">[Seleccione]</option>
                                                                    <option value="*">Sin Restricciones</option>
                                                                    @foreach($permissions as $key => $permisos)
                                                                    <option value="{{$permisos->slug}}">{{$permisos->name}}</option>                                           
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="displaytoken" class="col-md-12 text-center" style="display:none;">
                                                        <h5>Token De Acceso</h5>
                                                        <p id="newtoken"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Registrar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- MODAL ELIMINAR TOKENS-->
                            <div class="modal fade" id="modal-tokens-list">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Listado de Tokens Asignados al Usuario</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12"><h4> Rol Principal del usuario: <b><span id="RolPrincipal" style="color:red;"></span></b></h4></div>
                                                <br>
                                                <hr>
                                                <div class="col-md-12">                                                  
                                                    <table id="TokenListUser" class="table table-sm compact table-bordered table-striped text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>Nombre del Token</th>
                                                                <th>Token</th>                                                                                                                                                                                    
                                                                <th>Acciones</th>                                                                                                                                                                                     
                                                            </tr>
                                                        </thead>
                                                        <tbody>                                                       
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>                                          
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                        </div>
                        <!-- /.card-body -->
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
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2/css/select2.css')}}">  
<link media="all" type="text/css" rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endpush

@push('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.js')}}" type="text/javascript"></script>
<script src="{{asset('plugins/select2/js/select2.js')}}"></script>
<script src="{{asset('js/user/users.js')}}" type="text/javascript"></script>
@endpush