<div class="row mb-3">
    <div class="col-sm-2">
        {{ html()->label($label, $name)->class('col-form-label') }}
    </div>
    <div class="col-sm-10">
        {{ html()->select($name)->class('form-select')->disabled($disabled ?? false)->open() }}
        {{ isset($default) ? html()->option($default, $default_value ?? null) : '' }}
        @isset($coleccion)
            @foreach($coleccion as $elemento)
                {{ $opcion($elemento) }}
            @endforeach
        @endisset
        {{ html()->select()->close() }}
    </div>
</div>
