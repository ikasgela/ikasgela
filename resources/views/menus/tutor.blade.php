@include('layouts.sidebar.nav-title', [
    'text' => __('Tutor'),
])
@include('layouts.sidebar.nav-item', [
    'route' => route('tutor.index'),
    'text' => __('Group report'),
    'icon' => 'bi-list-check',
])
@include('layouts.sidebar.nav-item', [
    'route' => route('tutor.tareas_enviadas'),
    'text' => __('Activities per day'),
    'icon' => 'bi-bar-chart',
])
<li class="nav-item">
    @include('layouts.sidebar.dropdown', [
        'text' => __('Individual reports'),
        'icon' => 'bi-person-bounding-box',
        'collapse_id' => 'reports-collapse',
    ])
    <div class="collapse" id="reports-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-1 ms-4">
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('results.index'),
                'text' => __('Results'),
                'icon' => 'bi-mortarboard',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('archivo.outline'),
                'text' => __('Course progress'),
                'icon' => 'bi-graph-up',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('archivo.index'),
                'text' => __('Archived'),
                'icon' => 'bi-archive',
            ])
            @include('layouts.sidebar.nav-item-desplegable', [
                'route' => route('archivo.diario'),
                'text' => __('Activity journal'),
                'icon' => 'bi-journal-text',
            ])
        </ul>
    </div>
</li>
