<div class='btn-group'>
    {{ html()->form('POST', route($ruta, [$ids[$loop->index], $ids[$loop->index-1] ?? -1]))->open() }}
    <button title="{{ __('Up') }}"
            type="submit"
            {{ !isset($ids[$loop->index-1]) ? 'disabled' : '' }}
            class="btn btn-light btn-sm">
        <i class="fas fa-arrow-up"></i>
    </button>
    {{ html()->form()->close() }}

    {{ html()->form('POST', route($ruta, [$ids[$loop->index], $ids[$loop->index+1] ?? -1]))->open() }}
    <button title="{{ __('Down') }}"
            type="submit"
            {{ !isset($ids[$loop->index+1]) ? 'disabled' : '' }}
            class="btn btn-light btn-sm ms-1">
        <i class="fas fa-arrow-down"></i>
    </button>
    {{ html()->form()->close() }}
</div>
