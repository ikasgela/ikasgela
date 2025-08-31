@if($actividad->unidad->curso->mostrar_calificaciones)
    @if($actividad->tarea->estado > 10 && $actividad->tarea->estado != 30)
        @if(!is_null($actividad->tarea->feedback))
            <hr class="m-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="card m-3
                            {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 40 ? !$actividad->hasEtiqueta('examen') ? 'border-success' : 'border-secondary' : '' }}
                            {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 41 ? 'border-warning' : '' }}">
                        <div class="card-header
                                {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 40 ? !$actividad->hasEtiqueta('examen') ? 'text-bg-success' : 'text-bg-secondary' : '' }}
                                {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 41 ? 'text-bg-warning' : '' }}">
                            <i class="bi bi-megaphone"></i> {{ __('Feedback') }}
                        </div>
                        <div class="p-3">
                            <div class="media rounded line-numbers">
                                <div class="media-body overflow-auto">
                                    {!! links_galeria($actividad->tarea->feedback) !!}
                                </div>
                            </div>
                            <hr class="m-0">
                            <p class="text-muted small">
                                {{ __('Score') }}: @include('actividades.partials.puntuacion')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endif
