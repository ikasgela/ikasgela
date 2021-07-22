@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Users') }}</h1>
        <div class="form-inline">
            <div class="btn-toolbar" role="toolbar">
                {!! Form::open(['route' => ['users.index.filtro'], 'method' => 'POST']) !!}
                {!! Form::button(__('Clear filters'), ['type' => 'submit',
                    'class' => session('profesor_filtro_etiquetas') == 'S' ? 'btn btn-sm mx-1 btn-primary' : 'btn btn-sm mx-1 btn-outline-secondary'
                ]) !!}
                {!! Form::hidden('filtro_etiquetas','N') !!}
                {!! Form::close() !!}
            </div>
        </div>
        <div></div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin']))
        {!! Form::open(['route' => ['users.index.filtro'], 'method' => 'POST']) !!}
        @include('partials.desplegable_organizations')
        {!! Form::close() !!}
    @endif

    @include('users.partials.tabla_usuarios')
    @include('layouts.errors')

@endsection
