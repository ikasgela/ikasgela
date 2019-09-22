<li class="nav-title">{{ __('Admin') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('results.index') }}">
        <i class="nav-icon fas fa-graduation-cap"></i> {{ __('Results') }}
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
            <a class="nav-link" href="{{ route('markdown_texts.index') }}">
                <i class="fab fa-markdown"></i> {{ __('Markdown texts') }}
            </a>
        </li>
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
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cuestionarios.index') }}">
                <i class="fas fa-question-circle"></i> {{ __('Questionnaires') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('file_uploads.index') }}">
                <i class="fas fa-file-upload"></i> {{ __('File uploads') }}
            </a>
        </li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Structure') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizations.index') }}">
                {{ __('Organizations') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('periods.index') }}">
                {{ __('Periods') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('categories.index') }}">
                {{ __('Categories') }}
            </a>
        </li>
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
        <i class="nav-icon fas fa-database"></i> {{ __('Users') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('groups.index') }}">
                {{ __('Groups') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('teams.index') }}">
                {{ __('Teams') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">
                {{ __('Users') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('roles.index') }}">
                {{ __('Roles') }}
            </a>
        </li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Evaluation') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('qualifications.index') }}">
                {{ __('Qualifications') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('skills.index') }}">
                {{ __('Skills') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('feedbacks.index') }}">
                {{ __('Feedback') }}
            </a>
        </li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('registros.index') }}">
        <i class="nav-icon fas fa-stream"></i> {{ __('Records') }}
    </a>
</li>
