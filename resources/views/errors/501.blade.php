@extends('errors::illustrated-layout')

@section('code', '501')
@section('title', __('Not implemented'))

@section('image')
    <div style="background-image: url({{ asset('/svg/500.svg') }});"
         class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __($exception->getMessage()) ?: __('Sorry, the requested action is not available yet.'))
