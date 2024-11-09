@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3 p-0">
        <h1>{{ __('Results') }}
            @if(config('ikasgela.pdf_report_enabled') && !Auth::user()->baja_ansiedad)
                @if(!is_null($user->curso_actual()))
                    @if(!Auth::user()->hasAnyRole(['profesor', 'tutor']))
                        <a class="ms-3"
                           style="color:#ed2224" {{-- https://www.schemecolor.com/adobe-inc-logo-colors.php --}}
                           title="{{ __('Export to PDF') }}"
                           target="_blank"
                           href="{{ route('results.pdf') }}"><i class="fas fa-file-pdf"></i>
                        </a>
                    @else
                        {{ html()->form('POST', route('results.pdf'))->class('d-inline-flex')->open() }}
                        {{ html()->submit('<i class="fas fa-file-pdf"></i>')
                                ->class('btn btn-link py-0 ms-1 border-0')->style('color:#ed2224; font-size:inherit; line-height:inherit;') }}
                        {{ html()->hidden('user_id', request()->user_id) }}
                        {{ html()->form()->close() }}
                    @endif
                @endif
            @endif
        </h1>
        <h2 class="text-muted">{{ $curso?->pretty_name }}</h2>
    </div>

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        <div class="d-flex justify-content-end mb-3">
            <div class="btn-toolbar" role="toolbar">
                {{ html()->form('POST', route('users.limpiar_cache', [$user->id]))->open() }}
                {{ html()->submit(__('Reload results'))->class(['btn btn-sm btn-outline-secondary']) }}
                {{ html()->form()->close() }}
            </div>
        </div>
    @endif

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.resultados')
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        {{ html()->form('POST', route('results.alumno'))->open() }}
        @include('partials.desplegable_usuarios')
        {{ html()->form()->close() }}
    @endif

    @if($milestones->count() > 0)
        {{ html()->form('POST', route('results.milestone'))->open() }}
        @include('partials.desplegable_milestones')
        {{ html()->form()->close() }}
    @endif

    @if(!is_null($user->curso_actual()))

        @if(!Auth::user()->baja_ansiedad)
            @include('results.html.evaluacion_continua')
        @endif

        @if(!Auth::user()->baja_ansiedad)
            @include('results.partials.criterios_calificacion')
        @endif

        @if(!Auth::user()->baja_ansiedad)
            @include('results.html.desarrollo_competencias')
        @endif

        @include('results.html.actividades_completadas')

        @if(!Auth::user()->baja_ansiedad)
            @include('results.html.actividades_dia')
        @endif
    @else
        <div class="row">
            <div class="col-md-12">
                <p>{{ __("There's no data to show.") }}</p>
            </div>
        </div>
    @endif

@endsection
