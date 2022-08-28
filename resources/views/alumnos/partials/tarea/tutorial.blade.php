@switch($actividad->tarea->estado)
    @case(10)
        {{-- Nueva --}}
        @include('partials.tutorial', [
            'color' => 'c-callout-success',
            'texto' => trans('tutorial.para_comenzar')
        ])
        @break
    @case(20)
        {{-- Aceptada --}}
    @case(21)
        {{-- Feedback leído --}}
        @include('partials.tutorial', [
            'color' => 'c-callout-success',
            'texto' => trans('tutorial.completa_envia')
        ])
        @break
    @case(30)
        {{-- Enviada --}}
        @include('partials.tutorial', [
            'color' => 'c-callout-success',
            'texto' => trans('tutorial.pendiente_revisar')
        ])
        @break
    @case(40)
        {{-- Revisada: OK --}}
    @case(41)
        {{-- Revisada: ERROR --}}
        @include('partials.tutorial', [
            'color' => 'c-callout-success',
            'texto' => trans('tutorial.revisada', ['url' => route('archivo.index')])
        ])
        @break
    @case(42)
        {{-- Avance automático --}}
    @case(50)
        {{-- Terminada --}}
        @include('partials.tutorial', [
            'color' => 'c-callout-success',
            'texto' => trans('tutorial.terminada', ['url' => route('archivo.index')])
        ])
        @break
    @case(60)
        {{-- Archivada --}}
        @break
    @default
@endswitch
