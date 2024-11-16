<hr class="mt-0 mb-2">
<div class="card-body py-1 mb-1">
    <h6 class="text-center font-weight-bold mt-2">
        @switch($actividad->tarea->estado)
            @case(10)
                {{-- Nueva --}}
                {{ __('Not yet accepted') }}
                @break
            @case(20)
                {{-- Aceptada --}}
            @case(21)
                {{-- Feedback leído --}}
                {{ __('Preparing for submission') }}
                @break
            @case(30)
                {{-- Enviada --}}
                {{ __('Waiting for review') }}
                @break
            @case(40)
                {{-- Revisada: OK --}}
            @case(41)
                {{-- Revisada: ERROR --}}
                {{ __('Review complete') }}
                @break
            @case(50)
                {{-- Terminada --}}
                {{ __('Finished') }}
                @break
            @case(60)
                {{-- Archivada --}}
                @break
            @default
        @endswitch
    </h6>
    <ul class="progress-indicator">
        @switch($actividad->tarea->estado)
            @case(10)
                {{-- Nueva --}}
                <li><span class="bubble"></span>{{ trans_choice('tasks.accepted', 1) }}</li>
                <li><span class="bubble"></span>{{ __('Submitted') }}</li>
                <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                <li><span class="bubble"></span>{{ __('Finished') }}</li>
                @break
            @case(20)
                {{-- Aceptada --}}
            @case(21)
                {{-- Feedback leído --}}
                <li class="completed"><span class="bubble"></span>{{ trans_choice('tasks.accepted', 1) }}</li>
                <li><span class="bubble"></span>{{ __('Submitted') }}</li>
                <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                <li><span class="bubble"></span>{{ __('Finished') }}</li>
                @break
            @case(30)
                {{-- Enviada --}}
                <li class="completed"><span class="bubble"></span>{{ trans_choice('tasks.accepted', 1) }}</li>
                <li class="completed"><span class="bubble"></span>{{ __('Submitted') }}</li>
                <li><span class="bubble"></span>{{ __('Feedback available') }}</li>
                <li><span class="bubble"></span>{{ __('Finished') }}</li>
                @break
            @case(40)
                {{-- Revisada: OK --}}
            @case(41)
                {{-- Revisada: ERROR --}}
                <li class="completed"><span class="bubble"></span>{{ trans_choice('tasks.accepted', 1) }}</li>
                <li class="completed"><span class="bubble"></span>{{ __('Submitted') }}</li>
                <li class="completed"><span class="bubble"></span>{{ __('Feedback available') }}
                </li>
                <li><span class="bubble"></span>{{ __('Finished') }}</li>
                @break
            @case(42)
                {{-- Avance automático --}}
            @case(50)
                {{-- Terminada --}}
                <li class="completed"><span class="bubble"></span>{{ trans_choice('tasks.accepted', 1) }}</li>
                <li class="completed"><span class="bubble"></span>{{ __('Submitted') }}</li>
                <li class="completed"><span class="bubble"></span>{{ __('Feedback available') }}
                </li>
                <li class="completed"><span class="bubble"></span>{{ __('Finished') }}</li>
                @break
            @case(60)
                {{-- Archivada --}}
                @break
            @default
        @endswitch
    </ul>
</div>
