@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities')])

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['actividades.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_cursos')
        {!! Form::close() !!}
    @endif

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity') }}</a>
        @if(Route::currentRouteName() == 'actividades.index')
            {!! link_to_route('actividades.plantillas', $title = __('View templates only'), $parameters = [],
                    $attributes = ['class' => 'btn btn-link text-secondary']); !!}
        @else
            {!! link_to_route('actividades.index', $title = __('View all the activities'), $parameters = [],
                    $attributes = ['class' => 'btn btn-link text-secondary']); !!}
        @endif
    </div>

    @include('actividades.partials.tabla_actividades')

    @include('partials.paginador', ['coleccion' => $actividades])

@endsection
