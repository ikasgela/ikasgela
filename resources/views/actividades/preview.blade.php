@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => __('Preview')])

    @if(session('tutorial'))
        <div class="callout callout-success b-t-1 b-r-1 b-b-1">
            <small class="text-muted">{{ __('Tutorial') }}</small>
            <p>Vista previa de la actividad.</p>
        </div>
    @endif
    <div class="row mt-4">
        <div class="col-md-12">
            {{-- Tarjeta --}}
            <div class="card border-dark">
                <div class="card-header text-white bg-dark d-flex justify-content-between">
                    <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
                </div>
                <div class="card-body">
                    <h2>{{ $actividad->nombre }}</h2>
                    <p>{{ $actividad->descripcion }}</p>
                </div>
            </div>
            {{-- Fin tarjeta--}}
        </div>
        @foreach($actividad->youtube_videos()->get() as $youtube_video)
            <div class="col-md-6">
                @include('tarjetas.youtube_video')
            </div>
        @endforeach
        @foreach($actividad->intellij_projects()->get() as $intellij_project)
            <div class="col-md-6">
                @php($repositorio = $intellij_project->gitlab())
                @include('tarjetas.intellij_project')
            </div>
        @endforeach
    </div>
    @include('partials.backbutton')
@endsection