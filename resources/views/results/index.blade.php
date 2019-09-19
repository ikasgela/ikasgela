@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Results')])

    @if(session('tutorial'))
        <div class="callout callout-success b-t-1 b-r-1 b-b-1">
            <small class="text-muted">{{ __('Tutorial') }}</small>
            <p>Aquí aparecerán los resultados de las competencias asociadas al curso.</p>
        </div>
    @endif

    @if(Auth::user()->hasRole('profesor'))
        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        @include('partials.desplegable_usuarios')
        {!! Form::close() !!}
    @endif

    @include('partials.subtitulo', ['subtitulo' => __('Skills development')])

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

    @include('partials.subtitulo', ['subtitulo' => __('Completed activities')])

    @if($unidades->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>{{ __('Unit') }}</th>
                    <th class="text-center">{{ __('Base') }}</th>
                    <th class="text-center">{{ __('Extra') }}</th>
                    <th class="text-center">Repaso</th>
                </tr>
                </thead>
                <tbody>
                @foreach($unidades as $unidad)
                    <tr>
                        <td class="align-middle">{{ $unidad->nombre }}</td>
                        @php($porcentaje = $unidad->num_actividades('base') > 0 ? round($user->num_archivadas('base', $unidad->id)/$unidad->num_actividades('base')*100) : -1)
                        <td class="align-middle text-center {{ $porcentaje>=0 ? $porcentaje<50 ? 'bg-warning text-dark' : 'bg-success' : '' }}">
                            {{ $user->num_archivadas('base', $unidad->id).'/'. $unidad->num_actividades('base') }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $user->num_archivadas('extra', $unidad->id) }}
                        </td>
                        <td class="align-middle text-center">
                            {{ $user->num_archivadas('repaso', $unidad->id) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <p>No hay unidades.</p>
            </div>
        </div>
    @endif
@endsection
