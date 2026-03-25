@extends('errors::layout')
@section('images_error')
<img src="{{asset('img/errors/404 ERROR-02.png')}}">
@endsection
@section('title', __('Not Found'))
@section('code', '404')
@section('message', __($exception->getMessage() ?: __('No encontrado')))
