@extends('errors::illustrated-layout')

@section('code', '400')
@section('title', __('Bad request'))

@section('image')
    <div style="background-image: url({{ asset('/svg/404.svg') }});"
         class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __($exception->getMessage()) ?: __('Sorry, the request is not correct.'))
