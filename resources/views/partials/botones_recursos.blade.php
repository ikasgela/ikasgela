<td class="align-middle">
    <div class='btn-group'>
        <a title="{{ __('YouTube video') }}"
           href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
           class="btn btn-sm {{ $actividad->youtube_videos()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
            <i class="fab fa-youtube"></i>
        </a>
        <a title="{{ __('IntelliJ project') }}"
           href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
           class="btn btn-sm {{ $actividad->intellij_projects()->count() > 0 ? 'btn-primary' : 'btn-light' }}">
            <i class="fab fa-java"></i></a>
    </div>
</td>
