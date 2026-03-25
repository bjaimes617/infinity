@extends('errors::layout')
@section('title', __('Server Error'))
@section('images_error')
<img src="{{asset('img/errors/500 ERROR-01.png')}}">
@endsection
@section('code', '500')
@section('message', __($exception->getMessage() ?: __('Server Error')))