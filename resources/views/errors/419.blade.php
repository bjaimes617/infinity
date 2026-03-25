@extends('errors::layout')
@section('images_error')
<img src="{{asset('img/errors/419 ERROR-07.png')}}">
@endsection
@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __($exception->getMessage() ?: __('Esta Pagina a Expirado')))
