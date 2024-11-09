<div class="d-flex flex-row justify-content-between align-items-center">
    {{ html()->label(__('Unit'), 'unidad')->class('form-label m-0') }}
    <div class="flex-fill mx-3">
        {{ html()->select($nombre_variable ?? 'unidad_id')->class('form-select')->open() }}
        {{ html()->option(__('--- None ---')) }}
        @foreach($unidades as $unidad)
            {{ html()->option($unidad->full_name . ($unidad->visible ? '' : ' (' . __('hidden') . ')'),
                                $unidad->id,
                                session('profesor_'. $nombre_variable ?? 'unidad_id') == $unidad->id) }}
        @endforeach
        {{ html()->select()->close() }}
    </div>
    <div>
        {{ html()->submit(__('Filter'))->class('btn btn-primary') }}
    </div>
</div>
