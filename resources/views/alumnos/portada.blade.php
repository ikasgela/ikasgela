@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Courses'), 'subtitulo' => $organization->name])

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.matricula')
    ])

    @forelse($periods as $period)
        @once
            @php($total = 0)
        @endonce
        @foreach($period->categories as $category)
            @if($loop->first)
                <h3>{{ $period->name }}</h3>
            @endif
            @foreach($category->cursos as $curso)
                @if($loop->first)
                    <div class="row">
                @endif
                @if(!in_array($curso->id, $matricula) && $curso->matricula_abierta || in_array($curso->id, $matricula))
                    @include('alumnos.partials.tarjeta_curso')
                    @php($total += 1)
                @endif
                @if($loop->last)
                    </div>
                @endif
            @endforeach
            @if($loop->last && $total == 0)
                <div class="row">
                    <div class="col-12">
                        <p>{{ __('There are no courses available') }}.</p>
                    </div>
                </div>
            @endif
        @endforeach
    @empty
        <div class="row">
            <div class="col-12">
                <p>{{ __('There are no courses available') }}.</p>
            </div>
        </div>
    @endforelse
@endsection
