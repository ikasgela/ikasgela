@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New rule')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['rules.store']]) !!}

            <div class="form-group row">
                {!! Form::label('rule_group_id', __('Rule group'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <input type="hidden" name="rule_group_id" value="{{ $rule_group->id }}"/>
                    <label class="col-form-label">{{ $rule_group->id }}</label>
                </div>
            </div>

            {{ Form::campoTexto('propiedad', __('Property'), '', ['placeholder' => 'puntuacion | intentos']) }}
            {{ Form::campoTexto('operador', __('Operator'), '', ['placeholder' => '> | < | >= | <= | == | !=']) }}
            {{ Form::campoTexto('valor', __('Value'), '', ['placeholder' => '100']) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
