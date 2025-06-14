@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Courses'), 'subtitulo' => $organization->name])

    @if(Auth::user()->hasRole('profesor'))
        <div class="d-flex justify-content-end mb-3">
            <div class="btn-toolbar" role="toolbar">
                {{ html()->form('POST', route('users.portada.filtro'))->open() }}
                {{ html()->submit(session('users_filtro_cursos_no_disponibles') == 'S' ? __('Hide not available courses') : __('Show not available courses'))
                        ->class(['btn btn-sm ms-1', session('users_filtro_cursos_no_disponibles') == 'S' ? 'btn-primary' : 'btn-outline-secondary']) }}
                {{ html()->hidden('filtro_cursos_no_disponibles', 'S') }}
                {{ html()->form()->close() }}
            </div>
        </div>
    @endif

    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.matricula')
    ])

    @forelse($periods as $period)
        @once
            @php($total = 0)
        @endonce
        @foreach($period->categories as $category)
            @foreach($category->cursos as $curso)
                @if($loop->first)
                    <div class="row">
                @endif
                @if(!in_array($curso->id, $matricula) && $curso->matricula_abierta || in_array($curso->id, $matricula))
                    @if(session('users_filtro_cursos_no_disponibles') == 'S' && !$curso->disponible())
                        @include('alumnos.partials.tarjeta_curso')
                        @php($total += 1)
                    @elseif($curso->disponible())
                        @include('alumnos.partials.tarjeta_curso')
                        @php($total += 1)
                    @endif
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
