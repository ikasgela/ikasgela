@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit skill')])

    <div class="card">
        <div class="card-body">

            {!! Form::model($skill, ['route' => ['skills.update', $skill->id], 'method' => 'PUT']) !!}

            {{ Form::campoTexto('name', __('Name')) }}
            {{ Form::campoTexto('description', __('Description')) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
