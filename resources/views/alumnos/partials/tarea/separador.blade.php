@switch($actividad->tarea->estado)
    @case(10)
        {{-- Nueva --}}
    @case(20)
        {{-- Aceptada --}}
    @case(21)
        {{-- Feedback leído --}}
    @case(30)
        {{-- Enviada --}}
    @case(40)
        {{-- Revisada: OK --}}
    @case(41)
        {{-- Revisada: ERROR --}}
    @case(50)
        {{-- Terminada --}}
        <hr class="mt-0 mb-2">
        @break
    @case(60)
    @case(62)
    @case(64)
        {{-- Archivada --}}
        @break
    @default
@endswitch
