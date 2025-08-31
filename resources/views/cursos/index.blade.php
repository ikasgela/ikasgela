@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Courses'), 'subtitulo' => ''])

    <div class="mb-3">
        <a class="btn btn-primary" href="{{ route('cursos.create') }}">{{ __('New course') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Tags') }}</th>
                <th class="text-center">{{ __('Start/End dates') }}</th>
                <th class="text-center">{{ __('Open enrollment') }}</th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Simultaneous activities')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Activity deadline')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Minimum completed percent')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Minimum skills percent')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' =>  __('Minimum exams percent')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Minimum final exams percent')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Mandatory exams')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Maximum recoverable percent')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Silence notifications') ])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Normalize calification')])
                </th>
                <th class="text-center">
                    @include('profesor.partials.titulo-columna', ['titulo' => __('Proportional calification adjustment')])
                </th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td class="small">
                        {{ $curso->full_name }}
                        <br>
                        <span class="text-muted">{{ $curso->slug }}</span>
                    </td>
                    <td>@include('partials.etiquetas', ['etiquetas' => $curso->etiquetas()])</td>
                    <td class="small">
                        {{ $curso?->fecha_inicio ? $curso->fecha_inicio->isoFormat('L LT') : __('Undefined') }}
                        <br>
                        {{ $curso?->fecha_fin ? $curso->fecha_fin->isoFormat('L LT') : __('Undefined') }}
                    </td>
                    <td class="text-center {{ $curso->matricula_abierta ? 'bg-warning' : '' }}">
                        {{ $curso->matricula_abierta ? __('Yes') : __('No') }}
                    </td>
                    <td class="text-center">{{ $curso->max_simultaneas ?? __('Undefined') }}</td>
                    <td class="text-center">{{ $curso->plazo_actividad ?? __('Undefined') }}</td>
                    <td class="text-center">{{ $curso->minimo_entregadas ?? __('Undefined') }}</td>
                    <td class="text-center">{{ $curso->minimo_competencias ?? __('Undefined') }}</td>
                    <td class="text-center">{{ $curso->minimo_examenes ?? __('Undefined') }}</td>
                    <td class="text-center">{{ $curso->minimo_examenes_finales ?? __('Undefined') }}</td>
                    <td class="text-center">{{ $curso->examenes_obligatorios ? __('Yes') : __('No') }}</td>
                    <td class="text-center">{{ $curso->maximo_recuperable_examenes_finales ?? __('Undefined') }}</td>
                    <td class="text-center {{ $curso->silence_notifications ? 'bg-warning' : '' }}">
                        {{ $curso->silence_notifications ? __('Yes') : __('No') }}
                    </td>
                    <td class="text-center">@include('partials.check_yes_no', ['checked' => $curso->normalizar_nota])</td>
                    <td class="text-center">
                        @switch($curso->ajuste_proporcional_nota)
                            @case('media')
                                {{ __('Average') }}
                                @break
                            @case('mediana')
                                {{ __('Median') }}
                                @break
                            @default
                                {{ __('Undefined') }}
                        @endswitch
                    </td>
                    <td>
                        <div class="btn-toolbar flex-nowrap">
                            <div class='btn-group me-2'>
                                @if(!$usuario->isMatriculado($curso))
                                    {{ html()->form('POST', route('cursos.matricular', [$curso->id, $usuario->id]))->open() }}
                                    {{ html()->submit('<i class="bi bi-plus-lg"></i>')->class('btn btn-light btn-sm')->attribute('title', __('Enroll')) }}
                                    {{ html()->form()->close() }}
                                @endif
                            </div>
                            <div class='btn-group'>
                                <a title="{{ __('Edit') }}"
                                   href="{{ route('cursos.edit', [$curso->id]) }}"
                                   class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>

                                {{ html()->form('POST', route('cursos.export', $curso->id))->open() }}
                                {{ html()->submit('<i class="bi bi-download"></i>')->class(['btn btn-light btn-sm', 'rounded-0'])->attribute('title', __('Export course')) }}
                                {{ html()->form()->close() }}

                                {{ html()->form('DELETE', route('cursos.reset', $curso->id))->open() }}
                                {{ html()
                                    ->reset('<i class="bi bi-power text-danger"></i>')
                                    ->name('reset')
                                    ->class(['btn btn-light btn-sm', 'rounded-0'])
                                    ->attribute('title', __('Reset course'))
                                    ->attribute('onclick', "return confirm('" . __('Are you sure?') . "')")
                                }}
                                {{ html()->form()->close() }}

                                {{ html()->form('DELETE', route('cursos.destroy', $curso->id))->open() }}
                                @include('partials.boton_borrar', ['last' => true])
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
