@include('layouts.sidebar.nav-title', [
    'text' => __('Administrator'),
])
<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Activities'),
        'icon' => 'bi-person-walking',
        'collapse_id' => 'activities-collapse',
    ])
    <div class="collapse" id="activities-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-1 ms-4">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('actividades.plantillas'),
                'text' => __('Activity templates'),
                'icon' => 'bi-file-text',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('intellij_projects.copia'),
                'text' => __('Project cloner'),
                'icon' => 'bi-copy',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('intellij_projects.descargar'),
                'text' => __('Download projects'),
                'icon' => 'bi-download',
            ])
        </ul>
    </div>
</li>

{{--
<li class="c-sidebar-nav-title">{{ __('Admin') }}</li>
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon fas fa-database"></i> {{ __('Activities') }}
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('actividades.plantillas') }}">
                <i class="c-sidebar-nav-icon fas fa-file"></i> {{ __('Activity templates') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('intellij_projects.copia') }}">
                <i class="c-sidebar-nav-icon fas fa-copy"></i> {{ __('Project cloner') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('intellij_projects.descargar') }}">
                <i class="c-sidebar-nav-icon fas fa-download"></i> {{ __('Download projects') }}
            </a>
        </li>
    </ul>
</li>
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon fas fa-database"></i> {{ __('Resources') }}
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('markdown_texts.index') }}">
                <i class="c-sidebar-nav-icon fab fa-markdown"></i> {{ __('Markdown texts') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('youtube_videos.index') }}">
                <i class="c-sidebar-nav-icon fab fa-youtube"></i> {{ __('YouTube videos') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item bg-dark">
            <a class="c-sidebar-nav-link" href="{{ route('link_collections.index') }}">
                <i class="c-sidebar-nav-icon fas fa-link"></i> {{ __('Links') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item bg-dark">
            <a class="c-sidebar-nav-link" href="{{ route('file_resources.index') }}">
                <i class="c-sidebar-nav-icon fas fa-file"></i> {{ __('Files') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('cuestionarios.index') }}">
                <i class="c-sidebar-nav-icon fas fa-question-circle"></i> {{ __('Questionnaires') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item bg-dark">
            <a class="c-sidebar-nav-link" href="{{ route('file_uploads.index') }}">
                <i class="c-sidebar-nav-icon fas fa-file-upload"></i> {{ __('Image uploads') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('intellij_projects.index') }}">
                <i class="c-sidebar-nav-icon fab fa-java"></i> {{ __('IntelliJ projects') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link bg-dark" href="{{ route('selectors.index') }}">
                <i class="c-sidebar-nav-icon fas fa-code-branch"></i> {{ __('Selectors') }}
            </a>
        </li>
    </ul>
</li>
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon fas fa-database"></i> {{ __('Structure') }}
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('organizations.index') }}">
                {{ __('Organizations') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('periods.index') }}">
                {{ __('Periods') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('categories.index') }}">
                {{ __('Categories') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('cursos.index') }}">
                {{ __('Courses') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('unidades.index') }}">
                {{ __('Units') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('actividades.index') }}">
                {{ __('Activities') }}
            </a>
        </li>
    </ul>
</li>
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon fas fa-database"></i> {{ __('Users') }}
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('groups.index') }}">
                {{ __('Groups') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('teams.index') }}">
                {{ __('Teams') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('users.index') }}">
                {{ __('Users') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('roles.index') }}">
                {{ __('Roles') }}
            </a>
        </li>
    </ul>
</li>
<li class="c-sidebar-nav-item c-sidebar-nav-dropdown">
    <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
        <i class="c-sidebar-nav-icon fas fa-database"></i> {{ __('Evaluation') }}
    </a>
    <ul class="c-sidebar-nav-dropdown-items">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('qualifications.index') }}">
                {{ __('Qualifications') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('skills.index') }}">
                {{ __('Skills') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('feedbacks.index') }}">
                {{ __('Feedback') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('milestones.index') }}">
                {{ __('Milestones') }}
            </a>
        </li>
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('safe_exam.index') }}">
                {{ __('Safe Exam Browser') }}
            </a>
        </li>
    </ul>
</li>
<li class="c-sidebar-nav-item">
    <a class="c-sidebar-nav-link" href="{{ route('registros.index') }}">
        <i class="c-sidebar-nav-icon fas fa-stream"></i> {{ __('Records') }}
    </a>
</li>
--}}
