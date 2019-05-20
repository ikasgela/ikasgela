@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit qualification')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($qualification, ['route' => ['qualifications.update', $qualification->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}
            {{ Form::campoCheck('template', __('Template')) }}

            <div class="form-group row">
                {!! Form::label('skills_seleccionados', __('Skill'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col">
                    <label>{{ __('Selected') }}</label>
                    <select name="skills_seleccionados[]" multiple class="form-control" id="select1">
                        @foreach($skills_seleccionados as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
                    <button id="remove" type="button" class="btn btn-primary mx-1">&lt;</button>
                    <button id="add" type="button" class="btn btn-primary mx-1">&gt;</button>
                </div>
                <div class="col">
                    <label>{{ __('Available') }}</label>
                    <select multiple class="form-control" id="select2">
                        @foreach($skills_disponibles as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <button id="boton_guardar" type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ url()->previous() }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
