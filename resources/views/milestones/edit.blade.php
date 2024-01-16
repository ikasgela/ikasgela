@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit milestone')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($milestone, ['route' => ['milestones.update', $milestone->id], 'method' => 'PUT']) !!}

            {{ Form::hidden('curso_id', $milestone->curso->id) }}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('date', __('Date')) }}
            {{ Form::campoCheck('published', __('Published')) }}
            {{ Form::campoTexto('decimals', __('Decimals')) }}
            {{ Form::campoCheck('truncate', __('Truncate')) }}
            {{ Form::campoCheck('normalizar_nota', __('Normalize calification')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
