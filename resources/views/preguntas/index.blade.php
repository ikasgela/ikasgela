@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Questions')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('preguntas.create') }}">{{ __('New question') }}</a>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Questions')])

    @include('preguntas.tabla')

@endsection
