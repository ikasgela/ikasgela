<div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
        <div><i class="bi bi-youtube me-2"></i>{{ __('Video') }}</div>
        <div>
            @include('partials.modificar_recursos', ['ruta' => 'youtube_videos'])
            @include('partials.editar_recurso', ['recurso' => $youtube_video, 'ruta' => 'youtube_videos'])
        </div>
    </div>
    <div class="card-body">
        @include('partials.cabecera_recurso', ['recurso' => $youtube_video, 'ruta' => 'youtube_videos'])
        <div class="ratio ratio-16x9">
            {!! $youtube_video->video_html !!}
        </div>
    </div>
</div>
