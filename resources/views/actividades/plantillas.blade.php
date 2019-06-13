@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Activities')])

    @include('actividades.selector_unidad')

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('actividades.create') }}">{{ __('New activity template') }}</a>
    </div>

    @include('actividades.partials.tabla_actividades')
@endsection
