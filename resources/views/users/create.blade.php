@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New user')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('users.store'))->open() }}

            @include('components.label-value', [
                'label' => __('Organization'),
                'value' => organizacion(),
            ])

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

            @include('components.label-password', [
                'label' => __('Password'),
                'name' => 'password',
            ])

            <div class="row mb-3">
                <div class="col-sm-2">
                    {{ html()->label(__('Roles'), 'roles_seleccionados')->class('form-label') }}
                </div>
                <div class="col-sm-10">
                    @foreach($roles_disponibles as $rol)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="roles_seleccionados[]"
                                   id="rol_{{ $rol->id }}" value="{{ $rol->id }}">
                            <label class="form-check-label" for="rol_{{ $rol->id }}">{{ $rol->description }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
