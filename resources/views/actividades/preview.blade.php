@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Preview')])

    @include('actividades.partials.preview_siguiente')

    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Vista previa de la actividad.'
    ])

    <div class="row mt-4">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card border-dark">
                <div class="card-header text-white bg-dark d-flex justify-content-between">
                    <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
                </div>
                <div class="card-body">
                    @include('actividades.partials.encabezado_con_etiquetas')
                    <p>{{ $actividad->descripcion }}</p>
                </div>
                @include('partials.tarjetas_actividad')
            </div>
            {{-- Fin tarjeta--}}
        </div>
    </div>

    @if(Auth::user()->hasAnyRole(['admin','profesor']))
        @if($actividad->feedbacks->count() > 0)

            @include('partials.subtitulo', ['subtitulo' => __('Feedback messages')])

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($actividad->feedbacks as $feedback)
                        <tr>
                            <td>{{ $feedback->id }}</td>
                            <td>{{ $feedback->titulo }}</td>
                            <td>
                                {!! Form::open(['route' => ['feedbacks.destroy', $feedback->id], 'method' => 'DELETE']) !!}
                                <div class='btn-group'>
                                    <a title="{{ __('Edit') }}"
                                       href="{{ route('feedbacks.edit', [$feedback->id]) }}"
                                       class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
                                    @include('partials.boton_borrar')
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    @include('partials.backbutton')
@endsection
