@switch($actividad->tarea->estado)
    @case(10)   {{-- Nueva --}}
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Para comenzar la actividad, acéptala. A partir de ese momento tendrás acceso a sus recursos.'
    ])
    @break
    @case(20)   {{-- Aceptada --}}
    @case(21)   {{-- Feedback leído --}}
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Completa la actividad y, cuando esté lista, envíala para revisar.'
    ])
    @break
    @case(30)   {{-- Enviada --}}
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'La actividad está pendiente de revisar, recibirás un email cuando se complete la revisión.'
    ])
    @break
    @case(40)   {{-- Revisada: OK --}}
    @case(41)   {{-- Revisada: ERROR --}}
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'La actividad está revisada y tienes disponible el feedback.<br> Si se ha dado por buena,
        podrás darla por terminada y trasladarla al <a href="'. route('archivo.index') .'">archivo</a>. Si no,
        tendrás que mejorarla y volver a enviarla.'
    ])
    @break
    @case(42)   {{-- Avance automático --}}
    @case(50)   {{-- Terminada --}}
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'La actividad está terminada y puedes archivarla para que desaparezca del escritorio. Podrás
        verla en el <a href="'. route('archivo.index') .'">archivo</a>.'
    ])
    @break
    @case(60)   {{-- Archivada --}}
    @break
    @default
@endswitch

@if(Route::current()->getName() == 'archivo.show')
    @include('partials.tutorial', [
        'color' => 'c-callout-success',
        'texto' => 'Esta es una actividad archivada.'
    ])
@endif

<div class="row">
    <div class="col-md-12">
        {{-- Tarjeta --}}
        <div class="card border-dark">
            <div class="card-header text-white bg-dark d-flex justify-content-between">
                <span>{{ $actividad->unidad->curso->nombre }} » {{ $actividad->unidad->nombre }}</span>
                @if(isset($actividad->fecha_entrega))
                    @if($actividad->fecha_entrega->gt(\Carbon\Carbon::now()))
                        <div>{{ __('Remaining time') }}:
                            <span data-countdown="{{ $actividad->fecha_entrega }}"></span>
                        </div>
                    @else
                        <span>{{ __('Task expired') }}</span>
                    @endif
                @endif
                @if(isset($num_actividad))
                    <span>{{ $num_actividad }} {{ __('of') }} {{count($actividades)}}</span>
                @endif
            </div>
            <div class="card-body pb-1">
                <div class="d-flex flex-row flex-wrap justify-content-between align-items-baseline mb-3">
                    <div>
                        @include('actividades.partials.encabezado_con_etiquetas')
                        <p>{{ $actividad->descripcion }}</p>
                    </div>
                    @if(Auth::user()->hasRole('alumno'))
                        @include('actividades.partials.boton_pregunta')
                    @endif
                </div>
                <div class="mb-3">
                    <form method="POST"
                          action="{{ route('actividades.estado', [$actividad->tarea->id]) }}">
                        @csrf
                        @method('PUT')
                        @switch($actividad->tarea->estado)
                            @case(10)   {{-- Nueva --}}
                            <button type="submit" name="nuevoestado" value="20"
                                    class="btn btn-primary single_click">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Accept activity') }}
                            </button>
                            @break
                            @case(20)   {{-- Aceptada --}}
                            @case(21)   {{-- Feedback leído --}}
                            @if($actividad->envioPermitido())
                                @if(!isset($actividad->unidad->curso->fecha_fin) || $actividad->unidad->curso->fecha_fin->gt(\Carbon\Carbon::now()) || $actividad->hasEtiqueta('examen'))
                                    <button type="submit" name="nuevoestado" value="30"
                                            class="btn btn-primary mr-2 single_click">
                                        <i class="fas fa-spinner fa-spin"
                                           style="display:none;"></i> {{ __('Submit for review') }}</button>
                                @else
                                    <div class="alert alert-danger pb-0" role="alert">
                                        <p>El curso ha finalizado, no se admiten más envíos.</p>
                                    </div>
                                @endif
                            @endif
                            @if($actividad->hasEtiqueta('extra') && !is_null($actividad->siguiente))
                                <button type="submit" name="nuevoestado" value="71"
                                        class="btn btn-light single_click">
                                    <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Show next') }}
                                </button>
                            @endif()
                            @break
                            @case(30)   {{-- Enviada --}}
                            @if($actividad->auto_avance)
                                <div class="alert alert-success" role="alert">
                                    <p>Esta actividad es de avance automático, no hay revisión del profesor.</p>
                                    <button type="submit" name="nuevoestado" value="42"
                                            class="btn btn-success single_click">
                                        <i class="fas fa-spinner fa-spin"
                                           style="display:none;"></i> {{ __('Next step') }}
                                    </button>
                                </div>
                            @else
                                <button type="submit" name="nuevoestado" value="32"
                                        onclick="return confirm('{{ __('Are you sure?') }}\n\n{{ __('Reopening the activity cancels the submission and allows making corrections, but it has a 5 point penalty.') }}')"
                                        class="btn btn-secondary single_click">
                                    <i class="fas fa-spinner fa-spin"
                                       style="display:none;"></i> {{ __('Reopen activity') }}</button>
                            @endif
                            @if(config('app.debug'))
                                <button type="submit" name="nuevoestado" value="40"
                                        class="btn btn-success ml-3"> {{ __('Ok') }}
                                </button>
                                <button type="submit" name="nuevoestado" value="41"
                                        class="btn btn-danger"> {{ __('Error') }}
                                </button>
                            @endif
                            @break
                            @case(40)   {{-- Revisada: OK --}}
                            @case(42)   {{-- Avance automático --}}
                            <button type="submit" name="nuevoestado" value="60"
                                    class="btn btn-primary single_click">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Archive') }}
                            </button>
                            @break;
                            @case(41)   {{-- Revisada: ERROR --}}
                            <button type="submit" name="nuevoestado" value="21"
                                    class="btn btn-primary single_click">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Feedback read') }}
                            </button>
                            @break
                            @case(50)   {{-- Terminada --}}
                            <button type="submit" name="nuevoestado" value="60"
                                    class="btn btn-primary single_click">
                                <i class="fas fa-spinner fa-spin" style="display:none;"></i> {{ __('Archive') }}
                            </button>
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
                @case(21)   {{-- Feedback leído --}}
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
                        @case(21)   {{-- Feedback leído --}}
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
                        @case(21)   {{-- Feedback leído --}}
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
                        @case(42)   {{-- Avance automático --}}
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
            @switch($actividad->tarea->estado)
                @case(20)   {{-- Aceptada --}}
                @case(21)   {{-- Feedback leído --}}
                @case(60)   {{-- Archivada --}}
                <hr class="mt-0 mb-2">
                @include('partials.tarjetas_actividad')
                @break
                @default
            @endswitch
            @if($actividad->tarea->estado > 10 && $actividad->tarea->estado != 30)
                @if(!is_null($actividad->tarea->feedback))
                    <hr class="mt-0 mb-2">
                    <div class="row mt-3 mb-0 mx-2">
                        <div class="col-md-12">
                            <div class="card
                            {{ $actividad->tarea->estado == 40 ? 'border-success' : '' }}
                            {{ $actividad->tarea->estado == 41 ? 'border-warning' : '' }}">
                                <div class="card-header
                                {{ $actividad->tarea->estado == 40 ? 'bg-success' : '' }}
                                {{ $actividad->tarea->estado == 41 ? 'bg-warning text-dark' : '' }}">
                                    <i class="fas fa-bullhorn"></i> {{ __('Feedback') }}
                                </div>
                                <div class="card-body pb-0">
                                    <div class="line-numbers">{!! $actividad->tarea->feedback !!}</div>
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
        </div>
        {{-- Fin tarjeta--}}
    </div>
</div>
