@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Edit YouTube video')])

    <div class="card">
        <div class="card-body">

            {{ html()->modelForm($youtube_video, 'PUT', route('youtube_videos.update', $youtube_video->id))->open() }}

            @include('components.label-text', [
                'label' => __('Title'),
                'name' => 'titulo',
            ])
            @include('components.label-text', [
                'label' => __('Description'),
                'name' => 'descripcion',
            ])
            @include('components.label-text', [
                'label' => __('Code'),
                'name' => 'codigo',
            ])

            @if(isset($youtube_video->codigo))
                @include('components.label-link', [
                    'label' => __('URL'),
                    'link' => $youtube_video->codigo,
                    'value' => $youtube_video->codigo,
                    'target' => '_blank',
                ])
            @endif

            @include('partials.guardar_cancelar')
            @include('layouts.errors')
            {{ html()->closeModelForm() }}

        </div>
    </div>
@endsection
