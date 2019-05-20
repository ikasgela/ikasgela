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
                {!! Form::label('skills_seleccionados', __('Skills'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col-sm-10">
                    @foreach($skills_disponibles as $skill)
                        <div class="form-inline">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="skills_seleccionados[]"
                                       {{ $qualification->skills()->find($skill->id) ? 'checked' : '' }}
                                       id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                <label class="form-check-label" for="skill_{{ $skill->id }}">{{ $skill->name }}</label>
                            </div>
                            <input class="ml-3" maxlength="3" type="text"
                                   name="percentage_{{ $skill->id }}"
                                   @if($qualification->skills()->find($skill->id))
                                   value="{{ $qualification->skills()->find($skill->id)->pivot->percentage }}"/>
                            @else
                                value=""/>
                            @endif
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
