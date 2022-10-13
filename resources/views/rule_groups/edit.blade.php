@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit rule group')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($rule_group, ['route' => ['rule_groups.update', $rule_group->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('selector_id', __('Selector'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <input type="hidden" name="selector_id" value="{{ $selector->id }}"/>
                    <label class="col-form-label">{{ $selector->titulo }}</label>
                </div>
            </div>

            {{ Form::campoTexto('operador', __('Operator'), $rule_group->operador, ['placeholder' => 'and | or']) }}
            {{ Form::campoTexto('accion', __('Action'), $rule_group->accion, ['placeholder' => 'siguiente']) }}
            {{ Form::campoTexto('resultado', __('Result'), $rule_group->resultado, ['placeholder' => '10 (id_actividad)']) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>

    @include('partials.subtitulo', ['subtitulo' => __('Rules')])

    @include('rules.tabla', ['rules' => $rule_group->rules])

    <a class="btn btn-primary"
       href="{{ route('rules.anyadir', $rule_group) }}">{{ __('New rule') }}</a>

@endsection
