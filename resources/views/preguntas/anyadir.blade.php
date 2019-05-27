@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New question')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['preguntas.store']]) !!}

            <input type="hidden" name="accion" value="preguntas.anyadir"/>

            <div class="form-group row">
                {!! Form::label('cuestionario_id', __('Questionnaire'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <input type="hidden" name="cuestionario_id" value="{{ $cuestionario->id }}"/>
                    <label class="col-form-label">{{ $cuestionario->titulo }}</label>
                </div>
            </div>

            {{ Form::campoTexto('titulo', __('Title')) }}
            {{ Form::campoTexto('texto', __('Text')) }}
            {{ Form::campoCheck('multiple', __('Multiple')) }}
            {{ Form::campoCheck('respondida', __('Answered')) }}
            {{ Form::campoCheck('correcta', __('Correct')) }}
            {{ Form::campoTexto('imagen', __('Image')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
