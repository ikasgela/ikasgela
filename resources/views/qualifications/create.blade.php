@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New qualification')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('qualifications.store'))->open() }}

            @include('components.label-select', [
                'label' => __('Course'),
                'name' => 'curso_id',
                'coleccion' => $cursos,
                'opcion' => function ($curso) {
                        return html()->option($curso->full_name,
                            $curso->id,
                            old('curso_id', Auth::user()->curso_actual()?->id) == $curso->id);
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
                'checked' => true,
            ])

            <div class="form-group row mb-3">
                <div class="col-2">
                    {{ html()->label(__('Skills'), 'skills_seleccionados')->class('form-label') }}
                </div>
                <div class="col">
                    <h5 class="small">{{ __('Available') }}</h5>
                    <ul class="list-group">
                        @forelse($skills_disponibles as $skill)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-10 d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input my-0"
                                               name="skills_seleccionados[]"
                                               id="skill_{{ $skill->id }}" value="{{ $skill->id }}">
                                        <label class="form-check-label ms-2"
                                               for="skill_{{ $skill->id }}">{{ $skill->full_name }}</label>
                                    </div>
                                    <div class="col-2">
                                        <input class="form-control" type="number" min="0" max="100" step="1"
                                               name="percentage_{{ $skill->id }}"
                                               value="0"/>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p>{{ __('None') }}</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
