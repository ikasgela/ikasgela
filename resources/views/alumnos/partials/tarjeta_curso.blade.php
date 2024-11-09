<div class="col-12 col-sm-6 col-md-4">
    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted small">{{ $category->pretty_name }}</p>
            <h5 class="card-title text-primary">{{ $curso->nombre }}</h5>
            <p class="card-text" style="height: 6em;">{{ $curso->descripcion }}</p>
        </div>
        <div class="card-footer d-flex align-items-center" style="height: 4.5em">
            @if(setting_usuario('curso_actual') != $curso->id)
                @if(!in_array($curso->id, $matricula) && $curso->matricula_abierta)
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
