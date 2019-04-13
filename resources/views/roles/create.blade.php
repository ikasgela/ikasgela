@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New role')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => 'roles.store']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
