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
                            <option value="{{ $category->id }}">{{ $category->period->organization->name }}
                                - {{ $category->period->name }} - {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('nombre', __('Name')) }}
            {{ Form::campoTexto('descripcion', __('Description')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            <div class="form-group row">
                {!! Form::label('qualification_id', __('Qualification'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="qualification_id" name="qualification_id">
                        <option value="">{{ __('--- None ---') }}</option>
                        @foreach($qualifications as $qualification)
                            <option value="{{ $qualification->id }}">
                                {{ $qualification->organization->name }} - {{ $qualification->name }}
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
            {{ Form::campoCheck('examenes_obligatorios', __('Mandatory exams')) }}

            {{ Form::campoTexto('fecha_inicio', __('Start date')) }}
            {{ Form::campoTexto('fecha_fin', __('End date')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
