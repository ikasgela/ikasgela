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

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
