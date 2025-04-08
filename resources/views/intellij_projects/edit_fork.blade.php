@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit fork')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('PUT', route('intellij_projects.update_fork', ['intellij_project' => $intellij_project->id, 'actividad' => $actividad->id]))->open() }}

            @include('components.label-text', [
                'label' => __('Repository'),
                'name' => 'repositorio',
                'placeholder' => 'root/programacion.plantillas.proyecto-intellij-java',
                'value' => $repositorio->pivot->fork,
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
