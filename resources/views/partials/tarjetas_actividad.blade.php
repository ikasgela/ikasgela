@foreach($actividad->markdown_texts()->get() as $markdown_text)
    <div class="col-md-6">
        @include('markdown_texts.tarjeta', ['texto' => $markdown_text->markdown()])
    </div>
@endforeach
@foreach($actividad->cuestionarios()->get() as $cuestionario)
    <div class="col-md-6">
        @include('cuestionarios.tarjeta')
    </div>
@endforeach
@foreach($actividad->youtube_videos()->get() as $youtube_video)
    <div class="col-md-6">
        @include('tarjetas.youtube_video')
    </div>
@endforeach
@foreach($actividad->intellij_projects()->get() as $intellij_project)
    <div class="col-md-6">
        @include('tarjetas.intellij_project', ['repositorio' => $intellij_project->gitlab()])
    </div>
@endforeach
