@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit organization')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($organization, ['route' => ['organizations.update', $organization->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('slug', __('Slug')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
