@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Escritorio'])

    @if(count($actividades) > 0)
        @foreach($actividades as $actividad)
            @if($user->tutorial)
                <div class="callout callout-success b-t-1 b-r-1 b-b-1">
                    <small class="text-muted">{{ __('Help') }}</small>
                    <p>Para comenzar la actividad, acéptala. A partir de ese momento tendrás acceso a sus recursos.</p>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    {{-- Tarjeta --}}
                    <div class="card border-dark">
                        <div class="card-header text-white bg-dark ">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</div>
                        <div class="card-body pb-1">
                            <h2>{{ $actividad->nombre }}</h2>
                            <p>{{ $actividad->descripcion }}</p>

                            <div class="mb-3">
                                <form method="POST" action="">
                                    <button type="submit" class="btn btn-primary">{{ __('Accept') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Submit for review') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Feedback read') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Archive') }}</button>
                                </form>
                            </div>
                        </div>
                        <hr class="mt-0 mb-2">
                        <div class="card-body py-1">
                            <h6 class="text-center font-weight-bold mt-2">{{ __('Preparing for submission') }}</h6>
                            <ul class="progress-indicator">
                                <li class="completed"><span class="bubble"></span>{{ __('Accepted') }}</li>
                                <li><span class="bubble"></span>{{ __('Submitted') }}</li>
                                <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                                <li><span class="bubble"></span>{{ __('Finished') }}</li>
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
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-md-12">
                <p>No tienes tareas asignadas.</p>
            </div>
        </div>
    @endif
@endsection
