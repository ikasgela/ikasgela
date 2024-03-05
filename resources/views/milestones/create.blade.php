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
            {{ Form::campoTexto('decimals', __('Decimals')) }}
            {{ Form::campoCheck('truncate', __('Truncate')) }}
            {{ Form::campoCheck('normalizar_nota', __('Normalize calification')) }}

            <div class="form-group row">
                {!! Form::label('ajuste_proporcional_nota', __('Proportional calification adjustment'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="ajuste_proporcional_nota" name="ajuste_proporcional_nota">
                        <option value="">{{ __('--- None --- ') }}</option>
                        <option value="media">{{ __('Average') }}</option>
                        <option value="mediana">{{ __('Median') }}</option>
                    </select>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
