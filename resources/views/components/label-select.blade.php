<div class="row mb-3">
    <div class="col-sm-2 d-flex align-items-end">
        {{ html()->label($label, $name)->class('form-label') }}
    </div>
    <div class="col-sm-10">
        {{ html()->select($name)->class('form-select')->open() }}
        {{ isset($default) ? html()->option($default, $default_value ?? null) : '' }}
        @foreach($coleccion as $elemento)
            {{ $opcion($elemento) }}
        @endforeach
        {{ html()->select()->close() }}
    </div>
</div>
