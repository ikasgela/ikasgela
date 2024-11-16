{{ html()->form('POST', route($ruta, $id))->open() }}
{{ html()->submit('<i class="fas fa-copy"></i>')
    ->class(['btn btn-light btn-sm',
        'rounded-end-0' => $first ?? false,
        'rounded-start-0' => $last ?? false,
        'rounded-0' => $middle ?? false,
        ])
    ->attribute('title', __('Duplicate')) }}
{{ html()->form()->close() }}
