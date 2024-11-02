{{ html()
    ->submit('<i class="fas fa-trash text-danger"></i>')
    ->name('borrar')
    ->class(['btn btn-light btn-sm', 'rounded-start-0' => $last ?? false])
    ->attribute('title', __('Delete'))
    ->attribute('onclick', "return confirm('" . __('Are you sure?') . "')")
}}
