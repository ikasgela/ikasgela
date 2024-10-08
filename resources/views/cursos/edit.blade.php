@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit course')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($curso, ['route' => ['cursos.update', $curso->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('category_id', __('Category'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="category_id" name="category_id">
                        @foreach($categories as $category)
                            <option
                                value="{{ $category->id }}" {{ $curso->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->full_name }}
                            </option>
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
                    <select class="form-control" id="qualification_id" name="qualification_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($qualifications as $qualification)
                            <option
                                value="{{ $qualification->id }}" {{ $curso->qualification_id == $qualification->id ? 'selected' : '' }}>
                                {{ $qualification->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('max_simultaneas', __('Simultaneous activities')) }}
            {{ Form::campoTexto('plazo_actividad', __('Activity deadline')) }}

            {{ Form::campoTexto('minimo_entregadas', __('Minimum completed percent')) }}
            {{ Form::campoTexto('minimo_competencias', __('Minimum skills percent')) }}
            {{ Form::campoTexto('minimo_examenes', __('Minimum exams percent')) }}
            {{ Form::campoTexto('minimo_examenes_finales', __('Minimum final exams percent')) }}
            {{ Form::campoCheck('examenes_obligatorios', __('Mandatory exams')) }}
            {{ Form::campoTexto('maximo_recuperable_examenes_finales', __('Maximum recoverable percent')) }}

            {{ Form::campoTexto('fecha_inicio', __('Start date')) }}
            {{ Form::campoTexto('fecha_fin', __('End date')) }}

            {{ Form::campoCheck('progreso_visible', __('Show course progress')) }}
            {{ Form::campoCheck('silence_notifications', __('Silence notifications')) }}
            {{ Form::campoCheck('normalizar_nota', __('Normalize calification')) }}

            <div class="form-group row">
                {!! Form::label('ajuste_proporcional_nota', __('Proportional calification adjustment'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="ajuste_proporcional_nota" name="ajuste_proporcional_nota">
                        <option value="">{{ __('--- None --- ') }}</option>
                        <option value="media" {{ $curso->ajuste_proporcional_nota == 'media' ? 'selected' : '' }}>
                            {{ __('Average') }}
                        </option>
                        <option value="mediana" {{ $curso->ajuste_proporcional_nota == 'mediana' ? 'selected' : '' }}>
                            {{ __('Median') }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('tarea_bienvenida_id', __('Welcome task'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="tarea_bienvenida_id" name="tarea_bienvenida_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($curso->actividades()->plantilla()->orderBy('orden')->get() as $actividad)
                            <option
                                value="{{ $actividad->id }}" {{ $curso->tarea_bienvenida_id == $actividad->id ? 'selected' : '' }}>
                                {{ $actividad->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
