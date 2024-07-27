@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit user')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('avatar', __('Avatar'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    @include('users.partials.avatar', ['user' => $user, 'width' => 100])
                </div>
            </div>

            {{ Form::campoTexto('identifier', __('Identifier')) }}
            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('surname', __('Surname')) }}
            {{ Form::campoTexto('email', __('Email')) }}
            {{ Form::campoTexto('username', __('Username')) }}
            {{ Form::campoTexto('last_active', __('Last active')) }}
            {{ Form::campoTexto('blocked_date', __('Blocked')) }}

            {{ Form::campoTexto('max_simultaneas', __('Simultaneous activities')) }}
            {{ Form::campoTexto('tags', __('Tags')) }}

            {{ Form::campoCheck('baja_ansiedad', __('Low anxiety mode')) }}

            <div class="form-group row">
                {!! Form::label('roles_seleccionados', __('Roles'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
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

            <div class="form-group row">
                {!! Form::label('curso_id', __('Current course'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="curso_id" name="curso_id">
                        <option value="">{{ __('--- None --- ') }}</option>
                        @foreach($cursos_seleccionados as $curso)
                            <option value="{{ $curso->id }}" <?php if ($curso_actual == $curso->id) echo 'selected'; ?>>
                                {{ $curso->category->period->organization->name }}
                                - {{ $curso->nombre }}
                                - {{ $curso->category->period->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('users.partials.selector_cursos')

            <div class="form-group row">
                {!! Form::label('organizations_seleccionados', __('Organizations'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
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

            <div class="form-group">
                <button id="boton_guardar" type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ url()->previous() }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
