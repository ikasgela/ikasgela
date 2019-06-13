@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit qualification')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($qualification, ['route' => ['qualifications.update', $qualification->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('organization_id', __('Organization'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="organization_id" name="organization_id">
                        @foreach($organizations as $organization)
                            <option value="{{ $organization->id }}" <?php if ($qualification->organization_id == $organization->id) echo 'selected'; ?>>{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}
            {{ Form::campoCheck('template', __('Template')) }}

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
                                                   {{ $qualification->skills()->find($skill->id) ? 'checked' : '' }}
                                                   id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                            <label class="form-check-label"
                                                   for="skill_{{ $skill->id }}">{{ $skill->organization->name }}
                                                - {{ $skill->name }}</label>
                                        </div>
                                    </div>
                                    <div class="col-3 d-flex justify-content-end">
                                        <input class="form-control" type="number" min="0" max="100" step="1"
                                               name="percentage_{{ $skill->id }}"
                                               @if($qualification->skills()->find($skill->id))
                                               value="{{ $qualification->skills()->find($skill->id)->pivot->percentage }}"/>
                                        @else
                                            value="0"/>
                                        @endif
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
