@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Courses'), 'subtitulo' => $organization->name])

    @include('partials.tutorial', [
    'color' => 'c-callout-success',
    'texto' => 'Aquí puedes matricularte en otros cursos disponibles y cambiar de un curso a otro.'
    ])

    @forelse($periods as $period)
        <h3>{{ $period->name }}</h3>
        <div class="row">
            @forelse($period->categories as $category)
                @forelse($category->cursos as $curso)
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card mb-3">
                            <div class="card-body mb-4">
                                <p class="text-muted small">{{ $category->pretty_name }}</p>
                                <h5 class="card-title text-primary">{{ $curso->nombre }}</h5>
                                <p class="card-text">{{ $curso->descripcion }}</p>
                            </div>
                            <div class="card-footer d-flex">
                                @if(setting_usuario('curso_actual') != $curso->id)
                                    @if(!in_array($curso->id, $matricula))
                                        @if($curso->matricula_abierta)
                                            {!! Form::open(['route' => ['cursos.matricular', $curso->id, Auth::user()->id]]) !!}
                                            {!! Form::button(__('Enroll in this course'), ['type' => 'submit', 'class' => 'btn btn-sm btn-secondary mr-3']) !!}
                                            {!! Form::close() !!}
                                        @else
                                            <span class="py-1 text-muted">{{ __('Course not available') }}.</span>
                                        @endif
                                    @else
                                        {!! Form::open(['route' => ['cursos.curso_actual', $curso->id, Auth::user()->id]]) !!}
                                        {!! Form::button(__('Set as current course'), ['type' => 'submit', 'class' => 'btn btn-sm btn-primary mr-3']) !!}
                                        {!! Form::close() !!}
                                    @endif
                                @else
                                    <span class="py-1">{{ __('This is the current course') }}.</span>
                                @endif
                                <span class="p-1">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            @empty
                <div class="col-12">
                    <p>No hay categorías.</p>
                </div>
            @endforelse
        </div>
    @empty
        <div class="col-12">
            <p>No hay periodos.</p>
        </div>
    @endforelse
@endsection
