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
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Tags') }}</th>
                <th class="text-center">{{ __('Start/End dates') }}</th>
                <th class="text-center">{{ __('Open enrollment') }}</th>
                <th class="text-center">{{ __('Simultaneous activities') }}</th>
                <th class="text-center">{{ __('Activity deadline') }}</th>
                <th class="text-center">{{ __('Minimum completed percent') }}</th>
                <th class="text-center">{{ __('Minimum skills percent') }}</th>
                <th class="text-center">{{ __('Minimum exams percent') }}</th>
                <th class="text-center">{{ __('Minimum final exams percent') }}</th>
                <th class="text-center">{{ __('Mandatory exams') }}</th>
                <th class="text-center">{{ __('Maximum recoverable percent') }}</th>
                <th class="text-center">{{ __('Silence notifications') }}</th>
                <th class="text-center">{{ __('Normalize calification') }}</th>
                <th class="text-center">{{ __('Proportional calification adjustment') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->full_name }}</td>
                    <td>{{ $curso->slug }}</td>
                    <td>@include('partials.etiquetas', ['etiquetas' => $curso->etiquetas()])</td>
                    <td>
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
                        <div class='btn-group'>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('cursos.edit', [$curso->id]) }}"
                               class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>

                            {!! Form::open(['route' => ['cursos.export', [$curso->id]], 'method' => 'POST']) !!}
                            {!! Form::button('<i class="fas fa-download"></i>', ['type' => 'submit',
                                'class' => 'btn btn-light btn-sm', 'title' => __('Export course')
                            ]) !!}
                            {!! Form::close() !!}

                            {!! Form::open(['route' => ['cursos.reset', [$curso->id]], 'method' => 'DELETE']) !!}
                            <button title="{{ __('Reset course') }}"
                                    name="reset"
                                    type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                    class="btn btn-light btn-sm"><i class="fas fa-power-off text-danger"></i>
                            </button>
                            {!! Form::close() !!}

                            {!! Form::open(['route' => ['cursos.destroy', [$curso->id]], 'method' => 'DELETE']) !!}
                            @include('partials.boton_borrar')
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
