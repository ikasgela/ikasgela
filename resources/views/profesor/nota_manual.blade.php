@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Manual calification')])

    <div class="card mb-3">
        <div class="card-body">

            <h2>{{ $user->full_name }}</h2>
            <p>{{ $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre }}</p>

            {{ html()->form('POST', route('profesor.nota_manual.update', ['user' => $user->id, 'curso' => $curso->id]))->open() }}

            @include('components.label-text', [
                'label' => __('Calification'),
                'name' => 'nota',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
