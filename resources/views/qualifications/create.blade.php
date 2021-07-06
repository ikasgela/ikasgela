@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New qualification')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['qualifications.store']]) !!}

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
            {{ Form::campoCheck('template', __('Template'), true) }}

            <div class="form-group row">
                {!! Form::label('skills_seleccionados', __('Skills'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col-sm-6">
                    <ul class="list-group">
                        @foreach($skills_disponibles as $skill)
                            <li class="list-group-item">
                                <div class="row form-inline">
                                    <div class="col-9 d-flex justify-content-start">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   name="skills_seleccionados[]"
                                                   id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                            <label class="form-check-label"
                                                   for="skill_{{ $skill->id }}">{{ $skill->organization->name }}
                                                - {{ $skill->name }}</label>
                                        </div>
                                    </div>
                                    <div class="col-3 d-flex justify-content-end">
                                        <input class="ml-3 form-control" type="number" min="0" max="100" step="1"
                                               name="percentage_{{ $skill->id }}"
                                               value="0"/>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
