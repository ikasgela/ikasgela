<div class='btn-group'>
    <button title="{{ __('Markdown text') }}"
            class="btn btn-sm {{ $actividad->markdown_texts()->count() > 0 ? 'btn-secondary' : 'btn-light' }}">
        <i class="fab fa-markdown"></i>
    </button>
    <button title="{{ __('YouTube video') }}"
            class="btn btn-sm {{ $actividad->youtube_videos()->count() > 0 ? 'btn-secondary' : 'btn-light' }}">
        <i class="fab fa-youtube"></i>
    </button>
    <button title="{{ __('Question') }}"
            class="btn btn-sm {{ $actividad->cuestionarios()->count() > 0 ? 'btn-secondary' : 'btn-light' }}">
        <i class="fas fa-question-circle"></i>
    </button>
    <button title="{{ __('File upload') }}"
            class="btn btn-sm {{ $actividad->file_uploads()->count() > 0 ? 'btn-secondary' : 'btn-light' }}">
        <i class="fas fa-file-upload"></i>
    </button>
    <button title="{{ __('IntelliJ project') }}"
            class="btn btn-sm {{ $actividad->intellij_projects()->count() > 0 ? 'btn-secondary' : 'btn-light' }}">
        <i class="fab fa-java"></i>
    </button>
</div>
