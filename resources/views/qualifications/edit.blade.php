@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit qualification')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($qualification, ['route' => ['qualifications.update', $qualification->id], 'method' => 'PUT', 'id' => 'principal']) !!}

            <div class="form-group row">
                {!! Form::label('curso_id', __('Course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option
                                value="{{ $curso->id }}" {{ $qualification->curso_id == $curso->id ? 'selected' : '' }}>
                                {{ $curso->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}
            {{ Form::campoCheck('template', __('Template')) }}

            <div class="form-group row">
                {!! Form::label('skills_seleccionados', __('Skills'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col-sm-10">
                    <h5>{{ __('Assigned') }}</h5>
                    <ul class="list-group">
                        @php($index = 0)
                        @foreach($skills_asignados as $skill)
                            <li class="list-group-item ms-3">
                                <div class="row form-inline">
                                    <div class="col-7 d-flex justify-content-start">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   name="skills_seleccionados[]"
                                                   {{ $qualification->skills()->find($skill->id) ? 'checked' : '' }}
                                                   id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                            <label class="form-check-label"
                                                   for="skill_{{ $skill->id }}">{{ $skill->full_name }}</label>
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
                                    @if($qualification->skills()->find($skill->id))
                                        <div class='col-2 d-flex justify-content-end'>
                                            <div class="btn-group">
                                                {!! Form::open(['route' => ['qualifications.reordenar_skills', $qualification->id], 'method' => 'POST']) !!}
                                                <button title="{{ __('Up') }}"
                                                        type="submit"
                                                        {{ !isset($ids[$index-1]) ? 'disabled' : '' }}
                                                        class="btn {{ !isset($ids[$index-1]) ? 'btn-light' : 'btn-primary' }} btn-sm">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                                <input type="hidden" name="a1" value="{{ $ids[$index] }}">
                                                <input type="hidden" name="a2" value="{{ $ids[$index-1] ?? -1 }}">
                                                {!! Form::close() !!}

                                                {!! Form::open(['route' => ['qualifications.reordenar_skills', $qualification->id], 'method' => 'POST']) !!}
                                                <button title="{{ __('Down') }}"
                                                        type="submit"
                                                        {{ !isset($ids[$index+1]) ? 'btn-light disabled' : '' }}
                                                        class="btn {{ !isset($ids[$index+1]) ? 'btn-light' : 'btn-primary' }} btn-sm ms-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                                <input type="hidden" name="a1" value="{{ $ids[$index] }}">
                                                <input type="hidden" name="a2" value="{{ $ids[$index+1] ?? -1 }}">
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                        @php($index += 1)
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <h5 class="mt-3">{{ __('Available') }}</h5>
                    <ul class="list-group ms-3">
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
                                                   for="skill_{{ $skill->id }}">{{ $skill->full_name }}</label>
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

            <div class="form-group">
                <button type="submit" class="btn btn-primary single_click" form="principal">
                    <i class="fas fa-spinner fa-spin"
                       style="display:none;"></i> {{ isset($texto)? $texto : __('Save') }}</button>
                <a href="{!! anterior() !!}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
