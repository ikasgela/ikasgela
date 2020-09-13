@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New organization')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['organizations.store']]) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            {{ Form::campoCheck('registration_open', __('Registration open')) }}
            {{ Form::campoTexto('seats', __('Available seats')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
