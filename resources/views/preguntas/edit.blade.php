@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit question')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($pregunta, ['route' => ['preguntas.update', $pregunta->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('cuestionario_id', __('Questionnaire'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="cuestionario_id" name="cuestionario_id">
                        @foreach($cuestionarios as $cuestionario)
                            <option value="{{ $cuestionario->id }}" <?php if ($pregunta->cuestionario_id == $cuestionario->id) echo 'selected'; ?>>{{ $cuestionario->titulo }}</option>
                        @endforeach
                    </select>
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
