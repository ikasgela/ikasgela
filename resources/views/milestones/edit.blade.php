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

            <div class="form-group row">
                {!! Form::label('ajuste_proporcional_nota', __('Proportional calification adjustment'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="ajuste_proporcional_nota" name="ajuste_proporcional_nota">
                        <option value="">{{ __('--- None --- ') }}</option>
                        <option value="media" {{ $milestone->ajuste_proporcional_nota == 'media' ? 'selected' : '' }}>
                            {{ __('Average') }}
                        </option>
                        <option
                            value="mediana" {{ $milestone->ajuste_proporcional_nota == 'mediana' ? 'selected' : '' }}>
                            {{ __('Median') }}
                        </option>
                    </select>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
