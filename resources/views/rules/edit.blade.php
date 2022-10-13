@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit rule')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($rule, ['route' => ['rules.update', $rule->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('rule_group_id', __('Rule group'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <input type="hidden" name="rule_group_id" value="{{ $rule->rule_group->id }}"/>
                    <label class="col-form-label">{{ $rule->rule_group->id }}</label>
                </div>
            </div>

            {{ Form::campoTexto('propiedad', __('Property'), $rule->propiedad, ['placeholder' => 'puntuacion | intentos']) }}
            {{ Form::campoTexto('operador', __('Operator'), $rule->operador, ['placeholder' => '> | < | >= | <= | == | !=']) }}
            {{ Form::campoTexto('valor', __('Value'), $rule->valor, ['placeholder' => '100']) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
