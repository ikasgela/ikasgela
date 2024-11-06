@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Users'), 'subtitulo' => subdominio()])

    <div class="d-flex justify-content-end mb-3">
        <div class="btn-toolbar" role="toolbar">
            {{ html()->form('POST', route('users.index.filtro'))->open() }}
            {{ html()->submit(__('Clear filters'))
                    ->class(['btn btn-sm mx-1', session('profesor_filtro_etiquetas') == 'S' ? 'btn-primary text-light' : 'btn-outline-secondary']) }}
            {{ html()->hidden('filtro_etiquetas', 'N') }}
            {{ html()->form()->close() }}
        </div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin']))
        <div class="mb-3">
            {{ html()->form('POST', route('users.index.filtro'))->open() }}
            @include('partials.desplegable_organizations')
            {{ html()->form()->close() }}
        </div>
    @endif

    <div class="mb-3">
        <a class="btn btn-primary text-light" href="{{ route('users.create') }}">{{ __('New user') }}</a>
    </div>

    @include('users.partials.tabla_usuarios')
    @include('layouts.errors')

@endsection
