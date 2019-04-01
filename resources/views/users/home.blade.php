@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Escritorio'])

    <div class="callout callout-success b-t-1 b-r-1 b-b-1">
        <small class="text-muted">Ayuda</small>
        <br>
        <p>Para comenzar la actividad, acéptala. A partir de ese momento tendrás acceso a sus recursos.</p>
    </div>

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

                            <div class="mb-3">
                                <form method="POST" action="">
{{--                                    <button type="submit" class="btn btn-primary">Aceptar</button>--}}
                                    <button type="submit" class="btn btn-primary">Enviar para revisar</button>
{{--                                    <button type="submit" class="btn btn-primary">Feedback leído</button>--}}
{{--                                    <button type="submit" class="btn btn-primary">Archivar</button>--}}
                                </form>
                            </div>
                        </div>
                        <hr class="mt-0 mb-2">
                        <div class="card-body pb-1">
                            <ul class="progress-indicator">
                                <li class="completed"><span class="bubble"></span>Aceptada</li>
                                <li><span class="bubble"></span>Enviada</li>
                                <li class="no-bubble"><span class="bubble"></span>Revisando</li>
                                <li><span class="bubble"></span>Feedback recibido</li>
                                <li><span class="bubble"></span>Terminada</li>
                                <li><span class="bubble"></span>Archivada</li>
                            </ul>
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
