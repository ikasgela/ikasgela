@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <h1>{{ __('Results') }}
            @if(!is_null($user->curso_actual()))

                @if(config('app.debug'))
                    @if(!Auth::user()->hasAnyRole(['profesor', 'tutor']))
                        <a class="ml-3"
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
        <h2 class="text-muted font-xl">{{ !is_null($curso) ? $curso->category->period->organization->name.' » '.$curso->category->period->name.' » '.$curso->nombre : '' }}</h2>
    </div>

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Aquí aparecerán los resultados de las competencias asociadas al curso.'
    ])

    @if(Auth::user()->hasAnyRole(['profesor', 'tutor']))
        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        @include('partials.desplegable_usuarios')
        {!! Form::close() !!}
    @endif

    @if(!is_null($user->curso_actual()))

        @include('results.partials.evaluacion_continua')

        @include('results.partials.criterios_calificacion')

        @include('results.partials.desarrollo_competencias')

        @include('results.partials.actividades_completadas')

        @include('results.partials.actividades_dia')

    @else
        <div class="row">
            <div class="col-md-12">
                <p>{{ __("There's no data to show.") }}</p>
            </div>
        </div>
    @endif

@endsection
