@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Tareas asignadas'])

    <div class="row">
        @if(count($actividades) > 0)
            @foreach($actividades as $actividad)
                <div class="col-md-12">
                    {{-- Tarjeta --}}
                    <div class="card border-dark">
                        <div class="card-header text-white bg-dark ">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</div>
                        <div class="card-body pb-1">
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
            @endforeach
        @else
            <div class="col-md">
                <p>No tienes tareas asignadas.</p>
            </div>
        @endif
    </div>
@endsection
