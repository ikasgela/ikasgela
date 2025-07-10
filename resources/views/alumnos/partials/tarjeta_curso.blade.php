<div class="col-12 col-sm-6 col-md-4">
    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted small">{{ $category->pretty_name }}</p>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title text-primary m-0">{{ $curso->nombre }}</h5>
                @if(Auth::user()->hasRole('profesor') && $curso->disponible())
                    @php($recuento = $curso->recuento_enviadas())
                    @if($recuento > 0)
                        <div class="badge text-bg-danger fw-light ms-auto">
                            {{ $recuento }}
                        </div>
                    @endif
                    @php($recuento = Auth::user()->newThreadsCount($curso))
                    @if($recuento > 0)
                        <div class="badge text-bg-success fw-light ms-2">
                            {{ $recuento }}
                        </div>
                    @endif
                    @php($recuento = $curso->recuento_caducadas())
                    @if($recuento > 0)
                        <div class="badge text-bg-warning fw-light ms-2">
                            {{ $recuento }}
                        </div>
                    @endif
                @elseif(!$curso->disponible())
                    <div class="badge text-bg-secondary fw-light ms-2">
                        {{ __('Course not available') }}
                    </div>
                @endif
            </div>
            <p class="card-text" style="height: 6em;">{{ $curso->descripcion }}</p>
        </div>
        <div class="card-footer d-flex align-items-center" style="height: 4.5em">
            @if(setting_usuario('curso_actual') != $curso->id)
                @if(!in_array($curso->id, $matricula) && ($curso->matricula_abierta && $curso->disponible() || Auth::user()->hasRole('profesor')))
                    {{ html()->form('POST', route('cursos.matricular', [$curso->id, Auth::user()->id]))->open() }}
                    {{ html()->submit(__('Enroll in this course'))->class('btn btn-primary me-3') }}
                    {{ html()->form()->close() }}
                @elseif(in_array($curso->id, $matricula))
                    {{ html()->form('POST', route('cursos.curso_actual', [$curso->id, Auth::user()->id]))->open() }}
                    {{ html()->submit(__('Set as current course'))->class('btn btn-secondary me-3') }}
                    {{ html()->form()->close() }}
                @else
                    <span class="text-muted">{{ __('Course not available') }}.</span>
                @endif
            @else
                <span>{{ __('This is the current course') }}.</span>
            @endif
        </div>
    </div>
</div>
