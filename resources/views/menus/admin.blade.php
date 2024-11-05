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
<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Resources'),
        'icon' => 'bi-puzzle',
        'collapse_id' => 'resources-collapse',
    ])
    <div class="collapse" id="resources-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-1 ms-4">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('markdown_texts.index'),
                'text' => __('Markdown texts'),
                'icon' => 'bi-markdown',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('youtube_videos.index'),
                'text' => __('YouTube videos'),
                'icon' => 'bi-youtube',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('link_collections.index'),
                'text' => __('Links'),
                'icon' => 'bi-link',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('file_resources.index'),
                'text' => __('Files'),
                'icon' => 'bi-file-earmark',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('cuestionarios.index'),
                'text' => __('Questionnaires'),
                'icon' => 'bi-question-circle',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('file_uploads.index'),
                'text' => __('Image uploads'),
                'icon' => 'bi-upload',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('intellij_projects.index'),
                'text' => __('IntelliJ projects'),
                'icon' => 'bi-filetype-java',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('selectors.index'),
                'text' => __('Selectors'),
                'icon' => 'bi-shuffle',
            ])
        </ul>
    </div>
</li>
{{--
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
--}}

<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Evaluation'),
        'icon' => 'bi-patch-check',
        'collapse_id' => 'evaluation-collapse',
    ])
    <div class="collapse ps-3" id="evaluation-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-2">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('safe_exam.index'),
                'text' => __('Safe Exam Browser'),
                'icon' => 'bi-cone-striped',
            ])
        </ul>
    </div>
</li>

@include('layouts.sidebar.nav-item', [
    'route' => route('logs'),
    'text' => __('Logs'),
    'icon' => 'bi-bug',
    'target' => '_blank',
])

{{--
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
