<div class="mb-3">
    <form method="POST"
          action="{{ route('actividades.estado', [$tarea->id]) }}">
        @csrf
        @method('PUT')
        @switch($tarea->estado)
            @case(10)
                {{-- Nueva --}}
                @if(!$actividad->is_finished || $actividad->auto_avance)
                    {{-- Mostrar si no ha superado la fecha de entrega --}}
                    <button type="submit" name="nuevoestado" value="20"
                            class="btn btn-primary single_click">
                        <span class="spinner-border spinner-border-sm" style="display:none;"></span>
                        {{ __('Accept activity') }}
                    </button>
                @endif
                @break
            @case(20)
                {{-- Aceptada --}}
            @case(21)
                {{-- Feedback leído --}}
                @if($actividad->envioPermitido())
                    @if($actividad->unidad->curso->disponible() || $actividad->hasEtiqueta('examen'))
                        @if(!$actividad->is_expired)
                            {{-- Mostrar el botón si no ha superado el límite --}}
                            @if(!$actividad->auto_avance)
                                <button type="submit" name="nuevoestado" value="30"
                                        onclick="single_click_confirmar(event, this, '{{ __('Are you sure?') }}', '{{ __('This will submit the activity for review and show the next one if available.') }}');"
                                        class="btn btn-primary me-2">
                                    <span class="spinner-border spinner-border-sm"
                                       style="display:none;"></span> {{ __('Submit for review') }}</button>
                            @else
                                <button type="submit" name="nuevoestado" value="64"
                                        class="btn btn-primary single_click">
                                    <span class="spinner-border spinner-border-sm"
                                       style="display:none;"></span>
                                    @if(!is_null($actividad->siguiente))
                                        {{ __('Next activity') }}
                                    @else
                                        {{ __('Archive') }}
                                    @endif
                                </button>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-danger pb-0 pt-3" role="alert">
                            <p>{{ __('The course has ended; no more submissions are allowed.') }}</p>
                        </div>
                    @endif
                @endif
                @if($actividad->hasEtiqueta('extra') && !is_null($actividad->siguiente))
                    <button type="submit" name="nuevoestado" value="71"
                            class="btn btn-light single_click">
                        <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Show next') }}
                    </button>
                @endif
                @break
            @case(30)
                {{-- Enviada --}}
                @if($actividad->auto_avance)
                    <div class="alert alert-success" role="alert">
                        <p>{{ __('This is an automatically advancing activity, there is no teacher review.') }}</p>
                        <button type="submit" name="nuevoestado" value="42"
                                class="btn btn-success single_click">
                            <span class="spinner-border spinner-border-sm"
                               style="display:none;"></span> {{ __('Next step') }}
                        </button>
                    </div>
                @elseif(!$actividad->is_finished)
                    <button type="submit" name="nuevoestado" value="32"
                            @if(!$actividad->hasEtiqueta('examen'))
                                onclick="return confirm('{{ __('Are you sure?') }}\n\n{{ __('Reopening the activity cancels the submission and allows making corrections, but it has a 5 point penalty.') }}')"
                            @endif
                            class="btn btn-secondary single_click">
                        <span class="spinner-border spinner-border-sm"
                           style="display:none;"></span> {{ __('Reopen activity') }}</button>
                @endif
                @if(config('app.debug'))
                    <button type="submit" name="nuevoestado" value="40"
                            class="btn btn-success ms-3"> {{ __('Ok') }}
                    </button>
                    <button type="submit" name="nuevoestado" value="41"
                            class="btn btn-danger"> {{ __('Error') }}
                    </button>
                @endif
                @break
            @case(40)
                {{-- Revisada: OK --}}
            @case(42)
                {{-- Avance automático --}}
                <button type="submit" name="nuevoestado" value="60"
                        class="btn btn-primary single_click">
                    <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Archive') }}
                </button>
                @break;
            @case(41)
                {{-- Revisada: ERROR --}}
                <button type="submit" name="nuevoestado" value="21"
                        class="btn btn-primary single_click">
                    <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Feedback read') }}
                </button>
                @break
            @case(50)
                {{-- Terminada --}}
                <button type="submit" name="nuevoestado" value="60"
                        class="btn btn-primary single_click">
                    <span class="spinner-border spinner-border-sm" style="display:none;"></span> {{ __('Archive') }}
                </button>
                @break
            @case(60)
                {{-- Archivada --}}
                @break
            @default
        @endswitch
    </form>
</div>
