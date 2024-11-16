{{ html()->form('POST', route($ruta, $id))->open() }}
{{ html()->submit('<i class="fas fa-copy"></i>')->class(['btn btn-light btn-sm'])->attribute('title', __('Duplicate')) }}
{{ html()->form()->close() }}
