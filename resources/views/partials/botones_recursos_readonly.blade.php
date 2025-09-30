<div class='btn-group'>
    <button title="{{ __('Markdown text') }}"
            class="btn btn-sm {{ $actividad->markdown_texts()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-markdown"></i>
    </button>
    <button title="{{ __('YouTube video') }}"
            class="btn btn-sm {{ $actividad->youtube_videos()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-youtube"></i>
    </button>
    <a title="{{ __('Links') }}"
       class="btn btn-sm {{ $actividad->link_collections()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-link"></i>
    </a>
    <a title="{{ __('Files') }}"
       class="btn btn-sm {{ $actividad->file_resources()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-file-earmark"></i>
    </a>
    <button title="{{ __('Questionnaire') }}"
            class="btn btn-sm {{ $actividad->cuestionarios()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-question-circle"></i>
    </button>
    <button title="{{ __('Image upload') }}"
            class="btn btn-sm {{ $actividad->file_uploads()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-upload"></i>
    </button>
    <button title="{{ __('Flashcards') }}"
            class="btn btn-sm {{ $actividad->flash_decks()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-card-text"></i>
    </button>
    <button title="{{ __('IntelliJ project') }}"
            class="btn btn-sm {{ $actividad->intellij_projects()->count() > 0 ? 'btn-secondary' : 'bg-body-secondary' }}">
        <i class="bi bi-code-slash"></i>
    </button>
</div>
