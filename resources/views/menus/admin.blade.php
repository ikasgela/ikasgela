<li class="nav-title">{{ __('Admin') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('intellij_projects.copia') }}">
        <i class="nav-icon fas fa-copy"></i> {{ __('Project cloner') }}
    </a>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Structure') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cursos.index') }}">
                {{ __('Courses') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('unidades.index') }}">
                {{ __('Units') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('actividades.index') }}">
                {{ __('Activities') }}
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
                {{ __('YouTube videos') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('intellij_projects.index') }}">
                {{ __('IntelliJ projects') }}
            </a>
        </li>
    </ul>
</li>
