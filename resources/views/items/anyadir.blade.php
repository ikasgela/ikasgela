@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New item')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['items.store']]) !!}

            <div class="form-group row">
                {!! Form::label('pregunta_id', __('Question'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <input type="hidden" name="pregunta_id" value="{{ $pregunta->id }}"/>
                    <label class="col-form-label">{{ $pregunta->titulo }}</label>
                </div>
            </div>

            {{ Form::campoTexto('texto', __('Text')) }}
            {{ Form::campoCheck('correcto', __('Correct')) }}
            {{ Form::campoCheck('seleccionado', __('Selected')) }}
            {{ Form::campoTexto('feedback', __('Feedback')) }}
            {{ Form::campoTexto('orden', __('Order')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
