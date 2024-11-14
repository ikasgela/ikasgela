{{ html()->form('POST', route($resource.'s.toggle.'.$field, ['actividad' => $actividad->id, "$resource" => $$resource->id]))->open() }}
{{ html()->submit($$resource->pivote($actividad)->$field ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>')
            ->class(['btn btn-link'])->attribute('title', __('Results')) }}
{{ html()->form()->close() }}
