<hr class="m-0">
<div class="row px-3 pt-3">
    @if($actividad->hasEtiqueta('trabajo en equipo'))
        <div class="col-md-6">
            @include('teams.partials.tarjeta', ['teams' => $actividad->teams])
        </div>
    @endif
    @foreach($actividad->recursos as $recurso)
        <div class="col-md-{{ $recurso->pivote($actividad)->columnas ?: 6 }}">
            @switch($recurso::class)
                @case('App\Models\IntellijProject')
                    @livewire('tarjeta-intellij', ['actividad' => $actividad, 'intellij_project' => $recurso])
                    @break
                @case('App\Models\MarkdownText')
                    @include('markdown_texts.tarjeta', ['markdown_text' => $recurso, 'texto' => $recurso->markdown()])
                    @break
                @case('App\Models\YoutubeVideo')
                    @include('youtube_videos.tarjeta', ['youtube_video' => $recurso])
                    @break
                @case('App\Models\FileUpload')
                    @include('file_uploads.tarjeta', ['file_upload' => $recurso])
                    @break
                @case('App\Models\FileResource')
                    @include('file_resources.tarjeta', ['file_resource' => $recurso])
                    @break
                @case('App\Models\Cuestionario')
                    @include('cuestionarios.tarjeta', ['cuestionario' => $recurso])
                    @break
                @case('App\Models\LinkCollection')
                    @include('link_collections.tarjeta', ['link_collection' => $recurso])
                    @break
                @case('App\Models\Rubric')
                    @livewire('rubric-component', [
                        'actividad' => $actividad,
                        'rubric' => $recurso,
                        'rubric_is_qualifying' => Route::currentRouteName() == 'profesor.revisar',
                    ])
                    @break
                @case('App\Models\Selector')
                    @if(Auth::user()->hasAnyRole(['profesor', 'admin', 'tutor']))
                        @include('selectors.tarjeta', ['selector' => $recurso])
                    @endif
                    @break
            @endswitch
        </div>
    @endforeach
</div>
