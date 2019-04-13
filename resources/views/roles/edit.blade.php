@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit role')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
