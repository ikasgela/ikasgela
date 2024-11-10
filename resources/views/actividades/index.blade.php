@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities')])

    @if(Auth::user()->hasAnyRole(['admin']))
        <div class="mb-3">
            {{ html()->form('POST', route('actividades.index.filtro'))->open() }}
            @include('partials.desplegable_cursos')
            {{ html()->form()->close() }}
        </div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity') }}</a>
        @if(Route::currentRouteName() == 'actividades.index')
            {{ html()->a(route('actividades.plantillas'), __('View templates only'))->class('btn btn-link text-secondary') }}
        @else
            {{ html()->a(route('actividades.index'), __('View all the activities'))->class('btn btn-link text-secondary') }}
        @endif
    </div>

    @include('actividades.partials.tabla_actividades')

    @include('partials.paginador', ['coleccion' => $actividades])

@endsection
