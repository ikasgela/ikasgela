@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit user')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($user, 'PUT', route('users.update', $user->id))->open() }}

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Avatar'), 'avatar')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                </div>
            </div>

            @include('components.label-text', [
                'label' => __('Identifier'),
                'name' => 'identifier',
            ])
            @include('components.label-text', [
                'label' => __('Name'),
                'name' => 'name',
            ])
            @include('components.label-text', [
                'label' => __('Surname'),
                'name' => 'surname',
            ])
            @include('components.label-text', [
                'label' => __('Email'),
                'name' => 'email',
            ])
            @include('components.label-text', [
                'label' => __('Username'),
                'name' => 'username',
            ])
            @include('components.label-text', [
                'label' => __('Last active'),
                'name' => 'last_active',
            ])
            @include('components.label-text', [
                'label' => __('Blocked'),
                'name' => 'blocked_date',
            ])
            @include('components.label-text', [
                'label' => __('Simultaneous activities'),
                'name' => 'max_simultaneas',
            ])
            @include('components.label-text', [
                'label' => __('Tags'),
                'name' => 'tags',
            ])
            @include('components.label-check', [
                'label' => __('Low anxiety mode'),
                'name' => 'baja_ansiedad',
            ])

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Roles'), 'roles_seleccionados')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    @foreach($roles_disponibles as $rol)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="roles_seleccionados[]"
                                   {{ $user->hasRole($rol->name) ? 'checked' : '' }}
                                   id="rol_{{ $rol->id }}" value="{{ $rol->id }}">
                            <label class="form-check-label" for="rol_{{ $rol->id }}">{{ $rol->description }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            @include('components.label-select', [
                'label' => __('Current course'),
                'name' => 'curso_id',
                'coleccion' => $cursos_seleccionados,
                'opcion' => function ($curso) use ($curso_actual) {
                    return html()->option($curso->full_name,
                        $curso->id,
                        old('curso_id', $curso_actual) == $curso->id);
                },
                'default' => __('--- None --- '),
            ])

            @include('users.partials.selector_cursos')

            <div class="row mb-3">
                <div class="col-2">
                    {{ html()->label(__('Organizations'), 'organizations_seleccionados')->class('form-label') }}
                </div>
                <div class="col">
                    <label>{{ __('Selected') }}</label>
                    <select name="organizations_seleccionados[]" multiple class="form-control multi-select"
                            id="organizations-select1">
                        @foreach($organizations_seleccionados as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
                    <button data-selector="organizations" type="button" class="btn btn-primary btn-sm add">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button data-selector="organizations" type="button" class="btn btn-primary btn-sm ms-1 remove">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <div class="col">
                    <label>{{ __('Available') }}</label>
                    <select multiple class="form-control multi-select" id="organizations-select2">
                        @foreach($organizations_disponibles as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
