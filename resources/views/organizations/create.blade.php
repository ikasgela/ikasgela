@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New organization')])

    <div class="card">
        <div class="card-body">

            {!! Form::open(['route' => ['organizations.store']]) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
