@extends('layouts.app')

@section('content')

    <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
        <div>
            <h1>{{ __('Desktop') }}
                @include('partials.boton_recargar')
            </h1>
        </div>
        <div>
            @if(session('num_actividades') > 0)
                @if(session('num_actividades') == 1)
                    <h2 class="text-muted font-xl">Tienes una actividad en curso</h2>
                @else
                    <h2 class="text-muted font-xl">Tienes {{ session('num_actividades') }} actividades en curso</h2>
                @endif
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
               aria-controls="pills-home" aria-selected="true">Examen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
               aria-controls="pills-profile" aria-selected="false">En curso</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab"
               aria-controls="pills-contact" aria-selected="false">Enviadas</a>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            @include('alumnos.partials.panel_actividades', ['actividades' => []])
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            @include('alumnos.partials.panel_actividades', ['actividades' => $actividades])
        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
            @include('alumnos.partials.panel_actividades', ['actividades' => []])
        </div>
    </div>
@endsection
