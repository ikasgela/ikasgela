@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New qualification')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['qualifications.store']]) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}
            {{ Form::campoCheck('template', __('Template'), true) }}

            <div class="form-group row">
                {!! Form::label('skills_seleccionados', __('Skills'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col-sm-10">
                    @foreach($skills_disponibles as $skill)
                        <div class="form-inline">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="skills_seleccionados[]"
                                       id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                <label class="form-check-label" for="skill_{{ $skill->id }}">{{ $skill->name }}</label>
                            </div>
                            <input class="ml-3 form-control" type="number" min="0" max="100" step="1"
                                   name="percentage_{{ $skill->id }}"
                                   value="0"/>
                        </div>
                    @endforeach
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
