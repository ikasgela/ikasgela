@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New milestone')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['milestones.store']]) !!}

            {{ Form::hidden('curso_id', $curso_actual?->id) }}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('date', __('Date')) }}
            {{ Form::campoCheck('published', __('Published')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
