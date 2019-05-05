<li class="nav-title">{{ __('Teacher') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('profesor.index') }}">
        <i class="nav-icon fas fa-tasks"></i> {{ __('Control panel') }}
        <span class="badge badge-danger">{{ session('num_enviadas') }}</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('messages') }}">
        <i class="nav-icon fas fa-comment"></i> {{ __('Tutorship') }}
        <span class="badge badge-danger">@include('messenger.unread-count')</span>
    </a>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Activities') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('actividades.plantillas') }}">
                <i class="nav-icon fas fa-file"></i> {{ __('Activity templates') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('intellij_projects.copia') }}">
                <i class="nav-icon fas fa-copy"></i> {{ __('Project cloner') }}
            </a>
        </li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Resources') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('youtube_videos.index') }}">
                <i class="fab fa-youtube"></i> {{ __('YouTube videos') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('intellij_projects.index') }}">
                <i class="fab fa-java"></i> {{ __('IntelliJ projects') }}
            </a>
        </li>
    </ul>
</li>
