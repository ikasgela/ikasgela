{{ html()
    ->submit('<i class="bi bi-trash3 text-danger"></i>')
    ->name('borrar')
    ->class(['btn btn-light btn-sm',
        'rounded-end-0' => $first ?? false,
        'rounded-start-0' => $last ?? false,
        'rounded-0' => $middle ?? false,
        ])
    ->attribute('title', $title ?? __('Delete'))
    ->attribute('onclick', "single_click_confirmar(event, this, '" . __('Confirmation needed') ."', '". __('Are you sure?') ."', '". __('Confirm'). "', '". __('Cancel') ."');")
}}
