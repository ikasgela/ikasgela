@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit qualification')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($qualification, 'PUT', route('qualifications.update', $qualification->id))->id('principal')->open() }}

            @include('components.label-select', [
                'label' => __('Course'),
                'name' => 'curso_id',
                'coleccion' => $cursos,
                'opcion' => function ($curso) use ($qualification) {
                        return html()->option($curso->full_name,
                            $curso->id,
                            old('curso_id', $qualification->curso_id) == $curso->id);
                },
            ])

            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'description',
            ])
            @include('components.label-check', [
                'label' => __('Template'),
                'name' => 'template',
            ])

            <div class="form-group row mb-3">
                <div class="col-2">
                    {{ html()->label(__('Skills'), 'skills_seleccionados')->class('form-label') }}
                </div>
                <div class="col">
                    <h5 class="small">{{ __('Assigned') }}</h5>
                    <ul class="list-group mb-3">
                        @php($index = 0)
                        @forelse($skills_asignados as $skill)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-9 d-flex align-items-center">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   name="skills_seleccionados[]"
                                                   {{ $qualification->skills()->find($skill->id) ? 'checked' : '' }}
                                                   id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                            <label class="form-check-label"
                                                   for="skill_{{ $skill->id }}">{{ $skill->full_name }}</label>
                                        </div>
                                    </div>
                                    @if($qualification->skills()->find($skill->id))
                                        <div class='col-1 d-flex align-items-center'>
                                            <div class="btn-group">
                                                {{ html()->form('POST', route('qualifications.reordenar_skills', $qualification->id))->open() }}
                                                <button title="{{ __('Up') }}"
                                                        type="submit"
                                                        {{ !isset($ids[$index-1]) ? 'disabled' : '' }}
                                                        class="btn {{ !isset($ids[$index-1]) ? 'btn-light' : 'btn-primary' }} btn-sm">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                                <input type="hidden" name="a1" value="{{ $ids[$index] }}">
                                                <input type="hidden" name="a2" value="{{ $ids[$index-1] ?? -1 }}">
                                                {{ html()->form()->close() }}

                                                {{ html()->form('POST', route('qualifications.reordenar_skills', $qualification->id))->open() }}
                                                <button title="{{ __('Down') }}"
                                                        type="submit"
                                                        {{ !isset($ids[$index+1]) ? 'btn-light disabled' : '' }}
                                                        class="btn {{ !isset($ids[$index+1]) ? 'btn-light' : 'btn-primary' }} btn-sm ms-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                                <input type="hidden" name="a1" value="{{ $ids[$index] }}">
                                                <input type="hidden" name="a2" value="{{ $ids[$index+1] ?? -1 }}">
                                                {{ html()->form()->close() }}
                                            </div>
                                        </div>
                                        @php($index += 1)
                                    @endif
                                    <div class="col-2">
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
                        @empty
                            <p>{{ __('None') }}</p>
                        @endforelse
                    </ul>
                    <h5 class="small">{{ __('Available') }}</h5>
                    <ul class="list-group">
                        @forelse($skills_disponibles as $skill)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-10 d-flex align-items-center">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                   name="skills_seleccionados[]"
                                                   {{ $qualification->skills()->find($skill->id) ? 'checked' : '' }}
                                                   id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                            <label class="form-check-label"
                                                   for="skill_{{ $skill->id }}">{{ $skill->full_name }}</label>
                                        </div>
                                    </div>
                                    <div class="col-2">
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
                        @empty
                            <p>{{ __('None') }}</p>
                        @endforelse
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
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
