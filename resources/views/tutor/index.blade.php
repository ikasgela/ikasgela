@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Control panel') }}
            @include('partials.boton_recargar')
        </h1>
        <h2 class="text-muted font-xl">{{ $organization->name ?? '?' }}</h2>
    </div>

    @include('tutor.partials.tabla_usuarios')

@endsection
