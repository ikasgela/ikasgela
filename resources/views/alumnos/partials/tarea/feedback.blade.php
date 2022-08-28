@if($actividad->tarea->estado > 10 && $actividad->tarea->estado != 30)
    @if(!is_null($actividad->tarea->feedback))
        <hr class="mt-0 mb-2">
        <div class="row mt-3 mb-0 mx-2">
            <div class="col-md-12">
                <div class="card
                            {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 40 ? !$actividad->hasEtiqueta('examen') ? 'border-success' : 'border-secondary' : '' }}
                            {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 41 ? 'border-warning' : '' }}
                            {{ !$actividad->unidad->curso->disponible() ? 'border-secondary' : '' }}">
                    <div class="card-header
                                {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 40 ? !$actividad->hasEtiqueta('examen') ? 'bg-success' : 'bg-secondary' : '' }}
                                {{ $actividad->unidad->curso->disponible() && $actividad->tarea->estado == 41 ? 'bg-warning text-dark' : '' }}
                                {{ !$actividad->unidad->curso->disponible() ? 'bg-secondary' : '' }}">
                        <i class="fas fa-bullhorn"></i> {{ __('Feedback') }}
                    </div>
                    <div class="mx-3 mt-3 p-1">
                        <div class="media rounded line-numbers">
                            <div class="media-body overflow-auto">
                                {!! links_galeria($actividad->tarea->feedback) !!}
                            </div>
                        </div>
                        <hr class="mt-0 mb-2">
                        <p class="text-muted small">
                            {{ __('Score') }}: @include('actividades.partials.puntuacion')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
