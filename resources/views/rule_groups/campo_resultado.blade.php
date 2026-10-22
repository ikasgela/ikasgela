@php
    $actual = old('resultado', $resultado ?? null);
@endphp

@include('components.label-select', [
    'label' => __('Result'),
    'name' => 'resultado',
    'coleccion' => $actividades,
    'opcion' => function ($actividad) use ($actual) {
        return html()->option(
            $actividad->nombre,
            $actividad->id,
            (string) $actual === (string) $actividad->id
        );
    },
])
