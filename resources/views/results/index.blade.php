@extends('layouts.app')

@section('content')

    <div class="row mb-3">
        <div class="col-md">
            <h1>{{ __('Results') }}</h1>
        </div>
    </div>

    {{-- Tarjeta --}}
    <div class="card">
        <div class="card-header">{{ $curso->nombre }}</div>

        @foreach($skills_curso as $skill)
            <div class="card-body">
                <h5 class="card-title">{{ $skill->name }}</h5>
                <p class="ml-5">{{ $skill->description }}</p>
                <div class="ml-5 progress" style="height: 24px;">
                    @php($porcentaje = round($resultados[$skill->id]->tarea/$resultados[$skill->id]->actividad*100))
                    <div class="progress-bar
                    @if($porcentaje<50)
                            bg-warning text-dark
                    @else
                            bg-success
                    @endif
                            " role="progressbar"
                         style="width: {{ $porcentaje }}%;"
                         aria-valuenow="{{ $porcentaje }}"
                         aria-valuemin="0"
                         aria-valuemax="100">{{ $porcentaje }}%
                    </div>
                </div>
                <div class="text-muted small text-right">
                    {{ $resultados[$skill->id]->tarea }}/{{ $resultados[$skill->id]->actividad }}
                </div>
            </div>
            @if(!$loop->last)
                <hr class="mt-0 mb-2">
            @endif
        @endforeach
    </div>
    {{-- Fin tarjeta--}}

@endsection
