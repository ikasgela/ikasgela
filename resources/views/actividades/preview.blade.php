@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Preview')])

    @if(Auth::user()->hasAnyRole(['admin','profesor']))
        <div class="d-flex align-items-center mb-3">
            @include('actividades.partials.botones_navegar')
            <div class="ms-2"></div>
            @include('actividades.partials.preview_siguiente')
        </div>
    @endif

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.vista_previa')
    ])

    <div class="row mt-4">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card tarea-card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
                    @if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
                        <div>
                            <a title="{{ __('Edit') }}"
                               href="{{ route('actividades.edit', [$actividad->id]) }}"
                               class='text-white'><i class="fas fa-edit"></i></a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            @include('actividades.partials.encabezado_con_etiquetas')
                            <p>{{ $actividad->descripcion }}</p>
                        </div>
                        @if(Auth::user()->hasAnyRole(['admin','profesor']) && Route::currentRouteName() == 'actividades.preview')
                            <div class="d-flex">
                                @include('partials.botones_recursos')
                                <div class='btn-group ms-3'>
                                    <a title="{{ __('Sort resources') }}"
                                       href="{{ route('actividades.show', [$actividad->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-sort"></i></a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @include('partials.tarjetas_actividad')
            </div>
            {{-- Fin tarjeta--}}
        </div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin','profesor']))

        @include('partials.subtitulo', ['subtitulo' => __('Feedback messages')])

        @if($feedbacks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Order') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($feedbacks as $feedback)
                        <tr>
                            <td>{{ $feedback->id }}</td>
                            <td>{{ $feedback->titulo }}</td>
                            <td>
                                @include('partials.botones_reordenar', ['ruta' => 'feedbacks.reordenar'])
                            </td>
                            <td>
                                {{ html()->form('DELETE', route('feedbacks.destroy', $feedback->id))->open() }}
                                <div class='btn-group'>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('feedbacks.edit', [$feedback->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                    @include('partials.boton_borrar')
                                </div>
                                {{ html()->form()->close() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mb-3">
            <a class="btn btn-primary"
               href="{{ route('feedbacks.create_actividad', ['actividad' => $actividad->id]) }}">
                {{ __('New feedback message') }}
            </a>
        </div>
    @endif

    @include('partials.backbutton')
@endsection
