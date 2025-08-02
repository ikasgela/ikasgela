{{ html()
    ->submit('<i class="bi bi-trash3 text-danger"></i>')
    ->name('borrar')
    ->class(['btn btn-light btn-sm',
        'rounded-end-0' => $first ?? false,
        'rounded-start-0' => $last ?? false,
        'rounded-0' => $middle ?? false,
        ])
    ->attribute('title', __('Delete'))
    ->attribute('onclick', "return confirm('" . __('Are you sure?') . "')")
}}
