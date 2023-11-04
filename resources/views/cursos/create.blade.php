@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New course')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['cursos.store']]) !!}

            <div class="form-group row">
                {!! Form::label('category_id', __('Category'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="category_id" name="category_id">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}
            {{ Form::campoTexto('tags', __('Tags')) }}
            {{ Form::campoCheck('matricula_abierta', __('Open enrollment')) }}

            <div class="form-group row">
                {!! Form::label('qualification_id', __('Qualification'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="qualification_id" name="qualification_id" disabled>
                        <option value="">{{ __('--- None ---') }}</option>
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('max_simultaneas', __('Simultaneous activities'), 10) }}
            {{ Form::campoTexto('plazo_actividad', __('Activity deadline'), 7) }}

            {{ Form::campoTexto('minimo_entregadas', __('Minimum completed percent'), 80) }}
            {{ Form::campoTexto('minimo_competencias', __('Minimum skills percent'), 50) }}
            {{ Form::campoTexto('minimo_examenes', __('Minimum exams percent'), 50) }}
            {{ Form::campoTexto('minimo_examenes_finales', __('Minimum final exams percent'), 50) }}
            {{ Form::campoCheck('examenes_obligatorios', __('Mandatory exams')) }}
            {{ Form::campoTexto('maximo_recuperable_examenes_finales', __('Maximum recoverable percent'), 100) }}

            {{ Form::campoTexto('fecha_inicio', __('Start date')) }}
            {{ Form::campoTexto('fecha_fin', __('End date')) }}

            {{ Form::campoCheck('progreso_visible', __('Show course progress')) }}
            {{ Form::campoCheck('silence_notifications', __('Silence notifications')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
