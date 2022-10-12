@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit selector')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($selector, ['route' => ['selectors.update', $selector->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Rule groups')])

{{--
    @include('rule_groups.tabla')

    <a class="btn btn-primary" href="{{ route('rule_groups.anyadir', $selector) }}">{{ __('Add rule groups') }}</a>
--}}

@endsection
