@include('alumnos.partials.tarea.tutorial')

@if(Route::current()->getName() == 'archivo.show')
    @include('partials.tutorial', [
        'color' => 'success',
        'texto' => trans('tutorial.archivada')
    ])
@endif

@section('fancybox')
    <link rel="stylesheet" href="{{ asset('/js/jquery.fancybox.min.css') }}"/>
    <script src="{{ asset('/js/jquery.fancybox.min.js') }}" defer></script>
@endsection

<div class="row">
    <div class="col-md-12">
        {{-- Tarjeta --}}
        <div class="card tarea-card mb-3">
            @include('alumnos.partials.tarea.cabecera')
            <div class="card-body pb-1">
                <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
                    <div>
                        @include('actividades.partials.encabezado_con_etiquetas')
                        <p>{{ $actividad->descripcion }}</p>
                    </div>
                    @if(Auth::user()->hasRole('alumno') && !$actividad->hasEtiqueta('examen'))
                        @include('actividades.partials.boton_pregunta')
                    @elseif($actividad->hasEtiqueta('examen'))
                        @include('actividades.partials.puntuacion_examen')
                    @endif
                </div>
            </div>
            @if($actividad->hasEtiqueta('seb') && $user->curso_actual()?->token_valido() || !$actividad->hasEtiqueta('seb'))
                @if(!$actividad->auto_avance && $actividad->tarea->estado < 60 && !$actividad->is_expired)
                    @include('alumnos.partials.tarea.linea_progreso')
                @endif
                <div>
                    @switch($actividad->tarea->estado)
                        @case(20)
                            {{-- Aceptada --}}
                        @case(21)
                            {{-- Feedback leído --}}
                            @if(!$actividad->is_expired)
                                @include('partials.tarjetas_actividad')
                            @endif
                            @break
                        @case(60)
                        @case(64)
                            {{-- Archivada --}}
                            @include('partials.tarjetas_actividad')
                            @break
                        @default
                    @endswitch
                    @include('alumnos.partials.tarea.feedback')
                </div>
                <hr class="my-0">
                <div class="card-body pb-0">
                    @include('alumnos.partials.tarea.boton_accion')
                </div>
            @else
                <hr class="my-0">
                <div class="card-body pb-0">
                    <div class="alert alert-danger" role="alert">
                        <span>{{ __('This task is only available using Safe Exam Browser.') }}</span>
                    </div>
                </div>
            @endif
        </div>
        {{-- Fin tarjeta--}}
    </div>
</div>
