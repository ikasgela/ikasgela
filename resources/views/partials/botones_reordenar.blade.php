<div class='btn-group'>
    {!! Form::open(['route' => [$ruta, $ids[$loop->index], $ids[$loop->index-1] ?? -1], 'method' => 'POST']) !!}
    <button title="{{ __('Up') }}"
            type="submit"
            {{ !isset($ids[$loop->index-1]) ? 'disabled' : '' }}
            class="btn btn-light btn-sm">
        <i class="fas fa-arrow-up"></i>
    </button>
    {!! Form::close() !!}

    {!! Form::open(['route' => [$ruta, $ids[$loop->index], $ids[$loop->index+1] ?? -1], 'method' => 'POST']) !!}
    <button title="{{ __('Down') }}"
            type="submit"
            {{ !isset($ids[$loop->index+1]) ? 'disabled' : '' }}
            class="btn btn-light btn-sm ml-1">
        <i class="fas fa-arrow-down"></i>
    </button>
    {!! Form::close() !!}
</div>
