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
                'label' => __('Email for Gravatar'),
                'name' => 'gravatar_email',
            ])
            @include('components.label-text', [
                'label' => __('Username'),
                'name' => 'username',
            ])
            @include('components.label-datetime-readonly', [
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

            @include('components.dual-selector', [
                'label' => 'Organizations',
                'name' => 'organizations_seleccionados',
                'selected' => $organizations_seleccionados,
                'available' => $organizations_disponibles,
                'optionText' => 'name',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
