@extends('errors::layout')
@section('images_error')
<img src="{{asset('img/errors/503 ERROR -08.png')}}">
@endsection
@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __($exception->getMessage() ?: __('Servicios No disponibles')))
