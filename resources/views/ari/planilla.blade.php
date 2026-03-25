@extends('layouts.mainPlanillaAri')
@section('title', 'Planilla ARI')
@section('recursos_humanos','menu-is-opening menu-open')
@section('recursos_humanos_active','active')
@section('menu_ari','menu-is-opening menu-open')
@section('ari','active')
@section('generate.ari','active')
@section('content')

<div align="center">
    <iframe width="900" height="1450" src="{{route('generate.ari.view',['historial' => $historico, 'personal' => $personal, 'anio' => $anio, 'variacion' => $variacion])}}" scrolling="no" frameBorder="0"></iframe><br><br><br>
    <button class="btn btn-primary" onclick="window.print();return false;"><i class="fa fa-print"></i> Imprimir</button><br><br><br>
</div>            

@endsection

