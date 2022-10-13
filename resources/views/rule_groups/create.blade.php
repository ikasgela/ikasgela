@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New rule group')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['rule_groups.store']]) !!}

            <div class="form-group row">
                {!! Form::label('selector_id', __('Selector'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <input type="hidden" name="selector_id" value="{{ $selector->id }}"/>
                    <label class="col-form-label">{{ $selector->titulo }}</label>
                </div>
            </div>

            {{ Form::campoTexto('operador', __('Operator'), '', ['placeholder' => 'and | or']) }}
            {{ Form::campoTexto('accion', __('Action'), '', ['placeholder' => 'siguiente']) }}
            {{ Form::campoTexto('resultado', __('Result'), '', ['placeholder' => '10 (id_actividad)']) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
