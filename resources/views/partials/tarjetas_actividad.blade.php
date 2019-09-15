<div class="row mt-3 mb-0 mx-2">
@foreach($actividad->markdown_texts()->get() as $markdown_text)
    <div class="col-md-6">
        @include('markdown_texts.tarjeta', ['texto' => $markdown_text->markdown()])
    </div>
@endforeach
@foreach($actividad->youtube_videos()->get() as $youtube_video)
    <div class="col-md-6">
        @include('youtube_videos.tarjeta')
    </div>
@endforeach
@foreach($actividad->cuestionarios()->get() as $cuestionario)
    <div class="col-md-6">
        @include('cuestionarios.tarjeta')
    </div>
@endforeach
@foreach($actividad->file_uploads()->get() as $file_upload)
    <div class="col-md-6">
        @include('file_uploads.tarjeta')
    </div>
@endforeach
@foreach($actividad->intellij_projects()->get() as $intellij_project)
    <div class="col-md-6">
        @include('intellij_projects.tarjeta', ['repositorio' => $intellij_project->gitlab()])
    </div>
@endforeach
</div>
