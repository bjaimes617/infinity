@extends('errors::layout')
@section('images_error')
<img src="{{asset('img/errors/401 ERROR-05.png')}}">
@endsection
@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __($exception->getMessage() ?: __('Recursos no Autorizados')))
