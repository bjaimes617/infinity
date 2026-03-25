@extends('errors::layout')
@section('images_error')
<img src="{{asset('img/errors/403 ERROR-04.png')}}">
@endsection
@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: __('Acceso Denegado')))
