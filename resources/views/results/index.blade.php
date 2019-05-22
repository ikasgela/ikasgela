@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Results')])

    @if(session('tutorial'))
        <div class="callout callout-success b-t-1 b-r-1 b-b-1">
            <small class="text-muted">{{ __('Tutorial') }}</small>
            <p>Aquí aparecerán los resultados de las competencias asociadas al curso.</p>
        </div>
    @endif
    @if(count($skills_curso) > 0)
        {{-- Tarjeta --}}
        <div class="card">
            <div class="card-header">{{ $curso->nombre }}</div>
            <div class="card-body">
                @foreach ($skills_curso as $skill)
                    <h5 class="card-title">{{ $skill->name }}</h5>
                    <p class="ml-5">{{ $skill->description }}</p>
                    <div class="ml-5 progress" style="height: 24px;">
                        @php($porcentaje = $resultados[$skill->id]->actividad > 0 ? round($resultados[$skill->id]->tarea/$resultados[$skill->id]->actividad*100) : 0)
                        <div class="progress-bar {{ $porcentaje<50 ? 'bg-warning text-dark' : 'bg-success' }}"
                             role="progressbar"
                             style="width: {{ $porcentaje }}%;"
                             aria-valuenow="{{ $porcentaje }}"
                             aria-valuemin="0"
                             aria-valuemax="100">@if($porcentaje>0){{ $porcentaje }}%@endif
                        </div>
                    </div>
                    <div class="text-muted small text-right">
                        {{ $resultados[$skill->id]->tarea + 0 }}/{{ $resultados[$skill->id]->actividad + 0 }}
                    </div>
                    @if(!$loop->last)
                        <hr>
                    @endif
                @endforeach
            </div>
        </div>
        {{-- Fin tarjeta--}}
    @else
        <p>{{ __('No skills assigned.') }}</p>
    @endif
@endsection
