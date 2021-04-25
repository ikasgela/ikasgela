<div class="row mt-3 mb-0 mx-2">
    @foreach($actividad->teams as $team)
        <div class="col-md-6">
            @include('teams.partials.tarjeta', ['team' => $team])
        </div>
    @endforeach
    @foreach($actividad->markdown_texts as $markdown_text)
        <div class="col-md-6">
            @include('markdown_texts.tarjeta', ['texto' => $markdown_text->markdown()])
        </div>
    @endforeach
    @foreach($actividad->youtube_videos as $youtube_video)
        <div class="col-md-6">
            @include('youtube_videos.tarjeta')
        </div>
    @endforeach
    @foreach($actividad->file_resources as $file_resource)
        <div class="col-md-6">
            @include('file_resources.tarjeta')
        </div>
    @endforeach
    @foreach($actividad->cuestionarios as $cuestionario)
        <div class="col-md-6">
            @include('cuestionarios.tarjeta')
        </div>
    @endforeach
    @foreach($actividad->file_uploads as $file_upload)
        <div class="col-md-6">
            @include('file_uploads.tarjeta')
        </div>
    @endforeach
    @foreach($actividad->intellij_projects as $intellij_project)
        <div class="col-md-6">
            @include('intellij_projects.tarjeta', ['repositorio' => $intellij_project->repository()])
        </div>
    @endforeach
</div>
