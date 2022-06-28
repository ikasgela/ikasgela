@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New unit')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['unidades.store']]) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option
                                value="{{ $curso->id }}" {{ $curso->id == Auth::user()->curso_actual()?->id ? 'selected' : '' }}>
                                {{ $curso->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('codigo', __('Code')) }}
            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            <div class="form-group row">
                {!! Form::label('qualification_id', __('Qualification'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="qualification_id" name="qualification_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($qualifications as $qualification)
                            <option value="{{ $qualification->id }}">{{ $qualification->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('orden', __('Order')) }}
            {{ Form::campoTexto('tags', __('Tags')) }}

            {{ Form::campoTexto('fecha_disponibilidad', __('Availability date')) }}
            {{ Form::campoTexto('fecha_entrega', __('Due date')) }}
            {{ Form::campoTexto('fecha_limite', __('Deadline')) }}

            {{ Form::campoTexto('minimo_entregadas', __('Minimum percent')) }}

            {{ Form::campoCheck('visible', __('Visible'), true) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
