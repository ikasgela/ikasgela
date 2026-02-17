@include('components.dual-selector', [
    'label' => 'Courses',
    'name' => 'cursos_seleccionados',
    'selected' => $cursos_seleccionados,
    'available' => $cursos_disponibles,
    'optionText' => function($curso) {
        return $curso->nombre . ' - ' . $curso->category->period->name . ' (' . $curso->category->period->organization->name . ')';
    },
])
