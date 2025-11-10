<td class="align-middle">
    <div class="d-flex">
        <div class='btn-group'>
            <a title="{{ __('Markdown text') }}"
               href="{{ route('markdown_texts.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->markdown_texts()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-markdown"></i>
            </a>
            <a title="{{ __('YouTube video') }}"
               href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->youtube_videos()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-youtube"></i>
            </a>
            <a title="{{ __('Links') }}"
               href="{{ route('link_collections.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->link_collections()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-link"></i>
            </a>
            <a title="{{ __('Files') }}"
               href="{{ route('file_resources.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->file_resources()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-file-earmark"></i>
            </a>
            <a title="{{ __('Questionnaire') }}"
               href="{{ route('cuestionarios.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->cuestionarios()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-question-circle"></i>
            </a>
            <a title="{{ __('Image upload') }}"
               href="{{ route('file_uploads.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->file_uploads()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-upload"></i>
            </a>
            @if(config('app.env') != 'production')
                <a title="{{ __('Flashcards') }}"
                   href="{{ route('flash_decks.actividad', [$actividad->id]) }}"
                   class="btn btn-sm {{ $actividad->flash_decks()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                    <i class="bi bi-card-text"></i>
                </a>
            @endif
            <a title="{{ __('IntelliJ project') }}"
               href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->intellij_projects()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-code-slash"></i>
            </a>
        </div>
        <div class='btn-group ms-2'>
            <a title="{{ __('Rubric') }}"
               href="{{ route('rubrics.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->rubrics()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-ui-checks-grid"></i>
            </a>
            <a title="{{ __('Test results') }}"
               href="{{ route('test_results.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->test_results()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-plus-slash-minus"></i>
            </a>
            <a title="{{ __('Selector') }}"
               href="{{ route('selectors.actividad', [$actividad->id]) }}"
               class="btn btn-sm {{ $actividad->selectors()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
                <i class="bi bi-shuffle"></i>
            </a>
        </div>
    </div>
</td>
