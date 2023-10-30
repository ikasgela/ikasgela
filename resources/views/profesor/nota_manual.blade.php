@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Manual calification')])

    <div class="card">
        <div class="card-body">

            <h2>{{ $user->full_name }}</h2>
            <p>{{ $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre }}</p>

            {!! Form::open(['route' => ['profesor.nota_manual.update', $user->id, $curso->id], 'method' => 'POST']) !!}

            {{ Form::campoTexto('nota', __('Calification'), $nota) }}

            @include('partials.guardar_cancelar')

            @include('layouts.errors')
            {!! Form::close() !!}

        </div>
    </div>
@endsection
