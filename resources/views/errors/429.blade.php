@extends('errors::layout')
@section('images_error')
<img src="{{asset('img/errors/429 ERROR-06.png')}}">
@endsection
@section('title', __('Too Many Requests'))
@section('code', '429')

@section('message', __($exception->getMessage().' Debe Esperar '. max(0, $exception->getHeaders()["X-RateLimit-Reset"] - time())." Segundos" ?: __('Demasiadas solicitudes simultaneas')))