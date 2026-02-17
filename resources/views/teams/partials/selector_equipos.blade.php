@include('components.dual-selector', [
    'label' => 'Users',
    'name' => 'users_seleccionados',
    'selected' => $users_seleccionados,
    'available' => $users_disponibles,
    'optionText' => 'full_name',
])
