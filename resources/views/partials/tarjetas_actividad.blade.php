<div class="row mt-3 mb-0 mx-2">
    @if($actividad->hasEtiqueta('trabajo en equipo'))
        <div class="col-md-6">
            @include('teams.partials.tarjeta', ['teams' => $actividad->teams])
        </div>
    @endif
    @foreach($actividad->recursos as $recurso)
        <div class="col-md-6">
            @switch($recurso::class)
                @case('App\IntellijProject')
                @include('intellij_projects.tarjeta', ['intellij_project' => $recurso, 'repositorio' => $recurso->repository()])
                @break
                @case('App\MarkdownText')
                @include('markdown_texts.tarjeta', ['markdown_text' => $recurso, 'texto' => $recurso->markdown()])
                @break
                @case('App\YoutubeVideo')
                @include('youtube_videos.tarjeta', ['youtube_video' => $recurso])
                @break
                @case('App\FileUpload')
                @include('file_uploads.tarjeta', ['file_upload' => $recurso])
                @break
                @case('App\FileResource')
                @include('file_resources.tarjeta', ['file_resource' => $recurso])
                @break
                @case('App\Cuestionario')
                @include('cuestionarios.tarjeta', ['cuestionario' => $recurso])
                @break
            @endswitch
        </div>
    @endforeach
</div>
