{!! Form::open(['route' => [$resource.'s.toggle.'.$field, ['actividad' => $actividad->id, "$resource" => $$resource->id]], 'method' => 'POST']) !!}
{!! Form::button($$resource->pivote($actividad)->$field ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>', [
    'type' => 'submit',
    'class' => 'btn btn-link', 'title' => __('Results')
]) !!}
{!! Form::close() !!}
