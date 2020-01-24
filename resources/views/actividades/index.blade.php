@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities')])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity') }}</a>
        @if(Route::currentRouteName() == 'actividades.index')
            {!! link_to_route('actividades.plantillas', $title = 'Ver solo plantillas', $parameters = [],
                    $attributes = ['class' => 'btn btn-link text-secondary']); !!}
        @else
            {!! link_to_route('actividades.index', $title = 'Ver todas las actividades', $parameters = [],
                    $attributes = ['class' => 'btn btn-link text-secondary']); !!}
        @endif
    </div>

    @include('actividades.partials.tabla_actividades')

    <div class="d-flex justify-content-center">{{ $actividades->links() }}</div>
@endsection
