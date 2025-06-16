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
                'route' => route('rubrics.index'),
                'text' => __('Rubrics'),
                'icon' => 'bi-ui-checks-grid',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('selectors.index'),
                'text' => __('Selectors'),
                'icon' => 'bi-shuffle',
            ])
        </ul>
    </div>
</li>
<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Structure'),
        'icon' => 'bi-diagram-3',
        'collapse_id' => 'structure-collapse',
    ])
    <div class="collapse" id="structure-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-1 ms-4">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('organizations.index'),
                'text' => __('Organizations'),
                'icon' => 'bi-building',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('periods.index'),
                'text' => __('Periods'),
                'icon' => 'bi-calendar-range',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('categories.index'),
                'text' => __('Categories'),
                'icon' => 'bi-collection',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('cursos.index'),
                'text' => __('Courses'),
                'icon' => 'bi-easel',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('unidades.index'),
                'text' => __('Units'),
                'icon' => 'bi-list-ol',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('actividades.index'),
                'text' => __('Activities'),
                'icon' => 'bi-person-walking',
            ])
        </ul>
    </div>
</li>
<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Users'),
        'icon' => 'bi-people',
        'collapse_id' => 'users-collapse',
    ])
    <div class="collapse ps-3" id="users-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-2">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('groups.index'),
                'text' => __('Groups'),
                'icon' => 'bi-people',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('teams.index'),
                'text' => __('Teams'),
                'icon' => 'bi-people',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('users.index'),
                'text' => __('Users'),
                'icon' => 'bi-person',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('roles.index'),
                'text' => __('Roles'),
                'icon' => 'bi-person-badge',
            ])
        </ul>
    </div>
</li>
<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Evaluation'),
        'icon' => 'bi-patch-check',
        'collapse_id' => 'evaluation-collapse',
    ])
    <div class="collapse ps-3" id="evaluation-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ms-2">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('qualifications.index'),
                'text' => __('Qualifications'),
                'icon' => 'bi-clipboard2-check',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('skills.index'),
                'text' => __('Skills'),
                'icon' => 'bi-person-check',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('feedbacks.index'),
                'text' => __('Feedback'),
                'icon' => 'bi-megaphone',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('milestones.index'),
                'text' => __('Milestones'),
                'icon' => 'bi-signpost',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('safe_exam.index'),
                'text' => __('Safe Exam Browser'),
                'icon' => 'bi-cone-striped',
            ])
        </ul>
    </div>
</li>
@include('layouts.sidebar.nav-item', [
    'route' => route('registros.index'),
    'text' => __('Records'),
    'icon' => 'bi-database',
])
@include('layouts.sidebar.nav-item', [
    'route' => route('logs'),
    'text' => __('Logs'),
    'icon' => 'bi-bug',
    'target' => '_blank',
])
