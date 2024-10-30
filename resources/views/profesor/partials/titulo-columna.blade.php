@use('Illuminate\Support\Str')

<span title="{{ $titulo }}">
    {{ Str::substr($titulo, 0, 1) }}
</span>
