@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit team')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($team, ['route' => ['teams.update', $team->id], 'method' => 'PUT']) !!}

            <div class="form-group row">
                {!! Form::label('group_id', __('Group'), ['class' => 'col-sm-2 col-form-label']) !!}
                <div class="col-sm-10">
                    <select class="form-control" id="group_id" name="group_id">
                        @foreach($groups as $group)
                            <option
                                value="{{ $group->id }}" <?php if ($team->group_id == $group->id) echo 'selected'; ?>>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            <div class="form-group row">
                {!! Form::label('users_seleccionados', __('Users'), ['class' => 'col-sm-2 col-form-label pt-0']) !!}
                <div class="col">
                    <label>{{ __('Selected') }}</label>
                    <select name="users_seleccionados[]" multiple class="form-control multi-select"
                            id="users-select1">
                        @foreach($users_seleccionados as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
                    <button data-selector="users" type="button" class="btn btn-primary btn-sm add">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button data-selector="users" type="button" class="btn btn-primary btn-sm ml-1 remove">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <div class="col">
                    <label>{{ __('Available') }}</label>
                    <select multiple class="form-control multi-select" id="users-select2">
                        @foreach($users_disponibles as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <button id="boton_guardar" type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ url()->previous() }}" class="btn btn-link text-secondary">{{ __('Cancel') }}</a>
            </div>

            {{--            @include('partials.guardar_cancelar')--}}

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
