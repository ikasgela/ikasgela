@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New user')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['users.store']]) !!}

            <div class="form-group row">
                <label class="col-sm-2 col-form-label">{{ __('Organization') }}</label>
                <div class="col-sm-10 form-control-plaintext">{{ organizacion() }}</div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('surname', __('Surname')) }}
            {{ Form::campoTexto('email', __('Email')) }}
            {{ Form::campoPassword('password', __('Password')) }}

            <div class="form-group row">
                {!! Form::label('roles_seleccionados', __('Roles'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
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

            <div class="form-group">
                <button id="boton_guardar" type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ url()->previous() }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
