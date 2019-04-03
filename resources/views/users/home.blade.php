@extends('layouts.app')

@section('content')

    @include('partials.titular', ['titular' => 'Escritorio'])

    @if(count($actividades) > 0)
        @foreach($actividades as $actividad)
            @if(session('tutorial'))
                <div class="callout callout-success b-t-1 b-r-1 b-b-1 mb-4">
                    <small class="text-muted">{{ __('Tutorial') }}</small>
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
                                <form method="POST"
                                      action="{{ route('actividades.estado', [$actividad->tarea->id]) }}">
                                    @csrf
                                    @method('PUT')
                                    @switch($actividad->tarea->estado)
                                        @case(10)   {{-- Nueva --}}
                                        <button type="submit" name="nuevoestado" value="20"
                                                class="btn btn-primary">{{ __('Accept activity') }}</button>
                                        @break
                                        @case(20)   {{-- Aceptada --}}
                                        <button type="submit" name="nuevoestado" value="30"
                                                class="btn btn-primary">{{ __('Submit for review') }}</button>
                                        @break
                                        @case(30)   {{-- Enviada --}}
                                        @break
                                        @case(40)   {{-- Revisada: OK --}}
                                        <button type="submit" name="nuevoestado" value="50"
                                                class="btn btn-primary">{{ __('Feedback read') }}</button>
                                        @break;
                                        @case(41)   {{-- Revisada: ERROR --}}
                                        <button type="submit" name="nuevoestado" value="20"
                                                class="btn btn-primary">{{ __('Feedback read') }}</button>
                                        @break
                                        @case(50)   {{-- Terminada --}}
                                        <button type="submit" name="nuevoestado" value="60"
                                                class="btn btn-primary">{{ __('Archive') }}</button>
                                        @break
                                        @case(60)   {{-- Archivada --}}
                                        @break
                                        @default
                                    @endswitch
                                </form>
                            </div>
                        </div>
                        @switch($actividad->tarea->estado)
                            @case(10)   {{-- Nueva --}}
                            @case(20)   {{-- Aceptada --}}
                            @case(30)   {{-- Enviada --}}
                            @case(40)   {{-- Revisada: OK --}}
                            @case(41)   {{-- Revisada: ERROR --}}
                            @case(50)   {{-- Terminada --}}
                            <hr class="mt-0 mb-2">
                            @break
                            @case(60)   {{-- Archivada --}}
                            @break
                            @default
                        @endswitch
                        <div class="card-body py-1">
                            <h6 class="text-center font-weight-bold mt-2">
                                @switch($actividad->tarea->estado)
                                    @case(10)   {{-- Nueva --}}
                                    {{ __('Not yet accepted') }}
                                    @break
                                    @case(20)   {{-- Aceptada --}}
                                    {{ __('Preparing for submission') }}
                                    @break
                                    @case(30)   {{-- Enviada --}}
                                    {{ __('Waiting for review') }}
                                    @break
                                    @case(40)   {{-- Revisada: OK --}}
                                    @case(41)   {{-- Revisada: ERROR --}}
                                    {{ __('Review complete') }}
                                    @break
                                    @case(50)   {{-- Terminada --}}
                                    {{ __('Finished') }}
                                    @break
                                    @case(60)   {{-- Archivada --}}
                                    @break
                                    @default
                                @endswitch
                            </h6>
                            <ul class="progress-indicator">
                                @switch($actividad->tarea->estado)
                                    @case(10)   {{-- Nueva --}}
                                    <li><span class="bubble"></span>{{ __('Accepted') }}</li>
                                    <li><span class="bubble"></span>{{ __('Submitted') }}</li>
                                    <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                                    <li><span class="bubble"></span>{{ __('Finished') }}</li>
                                    @break
                                    @case(20)   {{-- Aceptada --}}
                                    <li class="completed"><span class="bubble"></span>{{ __('Accepted') }}</li>
                                    <li><span class="bubble"></span>{{ __('Submitted') }}</li>
                                    <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                                    <li><span class="bubble"></span>{{ __('Finished') }}</li>
                                    @break
                                    @case(30)   {{-- Enviada --}}
                                    <li class="completed"><span class="bubble"></span>{{ __('Accepted') }}</li>
                                    <li class="completed"><span class="bubble"></span>{{ __('Submitted') }}</li>
                                    <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                                    <li><span class="bubble"></span>{{ __('Finished') }}</li>
                                    @break
                                    @case(40)   {{-- Revisada: OK --}}
                                    @case(41)   {{-- Revisada: ERROR --}}
                                    <li class="completed"><span class="bubble"></span>{{ __('Accepted') }}</li>
                                    <li class="completed"><span class="bubble"></span>{{ __('Submitted') }}</li>
                                    <li class="completed"><span class="bubble"></span>{{ __('Feedback available') }}
                                    </li>
                                    <li><span class="bubble"></span>{{ __('Finished') }}</li>
                                    @break
                                    @case(50)   {{-- Terminada --}}
                                    <li class="completed"><span class="bubble"></span>{{ __('Accepted') }}</li>
                                    <li class="completed"><span class="bubble"></span>{{ __('Submitted') }}</li>
                                    <li class="completed"><span class="bubble"></span>{{ __('Feedback available') }}
                                    </li>
                                    <li class="completed"><span class="bubble"></span>{{ __('Finished') }}</li>
                                    @break
                                    @case(60)   {{-- Archivada --}}
                                    @break
                                    @default
                                @endswitch
                            </ul>
                        </div>
                    </div>
                    {{-- Fin tarjeta--}}
                </div>
                @if($actividad->tarea->estado > 10)
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
                @endif
            </div>
        @endforeach
    @else
        @if(session('tutorial'))
            <div class="callout callout-success b-t-1 b-r-1 b-b-1">
                <small class="text-muted">{{ __('Tutorial') }}</small>
                <p>Aquí aparecerán las actividades que tengas asignadas.</p>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <p>No tienes tareas asignadas.</p>
            </div>
        </div>
    @endif
@endsection
