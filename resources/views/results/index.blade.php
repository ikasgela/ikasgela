@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Results') }}
            @if(config('ikasgela.pdf_report_enabled') && !Auth::user()->baja_ansiedad)
                @if(!is_null($user->curso_actual()))
                    @if(!Auth::user()->hasAnyRole(['profesor', 'tutor']))
                        <a class="ms-3"
                           style="color:#ed2224" {{-- https://www.schemecolor.com/adobe-inc-logo-colors.php --}}
                           title="{{ __('Export to PDF') }}"
                           href="{{ route('results.pdf') }}"><i class="fas fa-file-pdf"></i>
                        </a>
                    @else
                        {!! Form::open(['route' => ['results.pdf'], 'method' => 'POST', 'class'=>'d-inline']) !!}
                        {!! Form::button('<i class="fas fa-file-pdf"></i>', [
                            'type' => 'submit',
                            'class'=>'btn btn-link',
                            'style'=>'color:#ed2224; font-size:inherit; display:inline; padding-top:0;',
                        ]) !!}
                        {!! Form::hidden('user_id',request()->user_id) !!}
                        {!! Form::close() !!}
                    @endif
                @endif
            @endif
        </h1>
        @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
            <div class="form-inline">
                <div class="btn-toolbar" role="toolbar">
                    {!! Form::open(['route' => ['users.limpiar_cache', [$user->id]], 'method' => 'POST', 'style' => 'display:inline']) !!}
                    {!! Form::button(__('Reload results'), ['type' => 'submit', 'class' => 'btn btn-sm btn-outline-secondary']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        @endif
        <h2 class="text-muted font-xl">{{ $curso?->pretty_name }}</h2>
    </div>

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.resultados')
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        @include('partials.desplegable_usuarios')
        {!! Form::close() !!}
    @endif

    @if($milestones->count() > 0)
        {!! Form::open(['route' => ['results.milestone'], 'method' => 'POST']) !!}
        @include('partials.desplegable_milestones')
        {!! Form::close() !!}
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
