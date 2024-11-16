@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('New IntelliJ project')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->form('POST', route('intellij_projects.store'))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-text', [
                'label' => __('Host'),
                'name' => 'host',
                'value' => 'gitea',
            ])
            @include('components.label-text', [
                'label' => __('Open with'),
                'name' => 'open_with',
            ])
            @include('components.label-text', [
                'label' => __('Repository'),
                'name' => 'repositorio',
                'placeholder' => 'root/programacion.plantillas.proyecto-intellij-java',
            ])

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->form()->close() }}

        </div>
    </div>
@endsection
