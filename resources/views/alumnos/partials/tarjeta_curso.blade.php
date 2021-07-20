<div class="col-12 col-sm-6 col-md-4">
    <div class="card mb-3">
        <div class="card-body">
            <p class="text-muted small">{{ $category->pretty_name }}</p>
            <h5 class="card-title text-primary">{{ $curso->nombre }}</h5>
            <p class="card-text" style="height: 6em;">{{ $curso->descripcion }}</p>
        </div>
        <div class="card-footer d-flex">
            @if(setting_usuario('curso_actual') != $curso->id)
                @if(!in_array($curso->id, $matricula) && $curso->matricula_abierta)
                    {!! Form::open(['route' => ['cursos.matricular', $curso->id, Auth::user()->id]]) !!}
                    {!! Form::button(__('Enroll in this course'), ['type' => 'submit', 'class' => 'btn btn-sm btn-secondary mr-3']) !!}
                    {!! Form::close() !!}
                @elseif(in_array($curso->id, $matricula))
                    {!! Form::open(['route' => ['cursos.curso_actual', $curso->id, Auth::user()->id]]) !!}
                    {!! Form::button(__('Set as current course'), ['type' => 'submit', 'class' => 'btn btn-sm btn-secondary mr-3']) !!}
                    {!! Form::close() !!}
                @else
                    <span class="py-1 text-muted">{{ __('Course not available') }}.</span>
                @endif
            @else
                <span class="py-1">{{ __('This is the current course') }}.</span>
            @endif
            <span class="p-1">&nbsp;</span>
        </div>
    </div>
</div>
