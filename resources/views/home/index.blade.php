@extends('layouts.main')
@section('title', 'Home')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">             
                        
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">            
            <div class="card-body">
                <div class="jumbotron" style="margin-top: 10px">                   
                    <h4 class="display-4">Bienvenid@, {{ ucwords(Auth::user()->name)}}</h4>
                    <p class="lead">A la plataforma {{env('APP_NAME')}}... una integración con las diferentes tecnologias de Directa Group, facilitando la gestión del contact center y la gerencia administrativa de la organización</p>
                    <p class="lead"><a class="btn btn-primary" href="{{ route('dashboard.personal') }}">Dashboard Personal</a></p>
                    <hr class="my-3"><img class="img-fluid float-md-right" src="{{asset('img/logo directa.png')}}" width="200px"> 
                    <p style="font-size: 16px;">Desarrollado por la Gerencia Ingenier&iacute;a de Software 
                        &COPY;{{date("Y")}}</p>
                    
                </div> 
            </div>            
        </div>        
    </section>     
</div>
@endsection