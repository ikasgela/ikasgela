@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New skill')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['skills.store']]) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}
            {{ Form::campoTexto('peso_examen', __('Exam weight')) }}

            {{ Form::campoTexto('minimo_competencias', __('Minimum percent')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
