@extends('layouts.app')

@section('content')

    @if(!is_null($curso))
        @include('partials.titular', ['titular' => __('Group report'), 'subtitulo' => $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre  ?? '' ])
    @else
        @include('partials.titular', ['titular' => __('Group report')])
    @endif

    @include('tutor.partials.tabla_usuarios')

@endsection
