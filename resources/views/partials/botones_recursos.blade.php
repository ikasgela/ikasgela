<td class="align-middle">
    <div class='btn-group'>
        <a title="{{ __('Markdown text') }}"
           href="{{ route('markdown_texts.actividad', [$actividad->id]) }}"
           class="btn btn-sm {{ $actividad->markdown_texts()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
            <i class="fab fa-markdown"></i>
        </a>
        <a title="{{ __('YouTube video') }}"
           href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
           class="btn btn-sm {{ $actividad->youtube_videos()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
            <i class="fab fa-youtube"></i>
        </a>
        <a title="{{ __('Question') }}"
           href="{{ route('cuestionarios.actividad', [$actividad->id]) }}"
           class="btn btn-sm {{ $actividad->cuestionarios()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
            <i class="fas fa-question-circle"></i>
        </a>
        <a title="{{ __('IntelliJ project') }}"
           href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
           class="btn btn-sm {{ $actividad->intellij_projects()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
            <i class="fab fa-java"></i>
        </a>
    </div>
</td>
