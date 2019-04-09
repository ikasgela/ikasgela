<td class="align-middle">
    <div class='btn-group'>
        <a title="{{ __('YouTube video') }}"
           href="{{ route('youtube_videos.actividad', [$actividad->id]) }}"
           class="btn btn-light btn-sm"><i class="fab fa-youtube"></i></a>
        <a title="{{ __('IntelliJ project') }}"
           href="{{ route('intellij_projects.actividad', [$actividad->id]) }}"
           class="btn btn-light btn-sm"><i class="fab fa-java"></i></a>
    </div>
</td>
