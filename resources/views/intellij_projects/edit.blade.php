@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit IntelliJ project')])

    <div class="card mb-3">
        <div class="card-body">

            {{ html()->modelForm($intellij_project, 'PUT', route('intellij_projects.update', $intellij_project->id))->open() }}

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

            @if(isset($repositorio['web_url']))
                @include('components.label-link', [
                    'label' => __('URL'),
                    'link' => $repositorio['web_url'],
                    'value' => $repositorio['web_url'],
                    'target' => '_blank',
                ])
            @endif

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
