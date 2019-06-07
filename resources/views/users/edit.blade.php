@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit user')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('email', __('Email')) }}
            {{ Form::campoTexto('username', __('Username')) }}

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
                {!! Form::label('cursos_seleccionados', __('Courses'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col">
                    <label>{{ __('Selected') }}</label>
                    <select name="cursos_seleccionados[]" multiple class="form-control" id="cursos-select1">
                        @foreach($cursos_seleccionados as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
                    <button data-selector="cursos" type="button" class="btn btn-primary mx-1 add">&lt;</button>
                    <button data-selector="cursos" type="button" class="btn btn-primary mx-1 remove">&gt;</button>
                </div>
                <div class="col">
                    <label>{{ __('Available') }}</label>
                    <select multiple class="form-control" id="cursos-select2">
                        @foreach($cursos_disponibles as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('organizations_seleccionados', __('Organizations'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col">
                    <label>{{ __('Selected') }}</label>
                    <select name="organizations_seleccionados[]" multiple class="form-control"
                            id="organizations-select1">
                        @foreach($organizations_seleccionados as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
                    <button data-selector="organizations" type="button" class="btn btn-primary mx-1 add">&lt;</button>
                    <button data-selector="organizations" type="button" class="btn btn-primary mx-1 remove">&gt;
                    </button>
                </div>
                <div class="col">
                    <label>{{ __('Available') }}</label>
                    <select multiple class="form-control" id="organizations-select2">
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
