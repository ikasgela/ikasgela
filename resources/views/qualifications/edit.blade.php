@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit qualification')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($qualification, ['route' => ['qualifications.update', $qualification->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}
            {{ Form::campoCheck('template', __('Template'), false) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
