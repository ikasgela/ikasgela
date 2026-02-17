{{--
    Componente genérico de selector dual para transferir elementos entre dos listas

    Parámetros requeridos:
    - $label: Etiqueta principal del selector
    - $name: Nombre base para el selector (ej: 'users', 'cursos')
    - $selected: Colección de elementos seleccionados
    - $available: Colección de elementos disponibles
    - $optionText: Closure o string que define cómo mostrar cada elemento

    Parámetros opcionales:
    - $optionValue: Closure o string para el valor de la opción (por defecto: 'id')
    - $selectClass: Clase CSS para los selects (por defecto: 'form-select')
    - $height: Altura de los selects (por defecto: '10em')
    - $labelFor: ID del elemento al que apunta el label principal (por defecto: $name . '_seleccionados')
--}}

@php
    $nameId = str_replace(['[', ']'], '', $name);
    $selectClass = $selectClass ?? 'form-select';
    $height = $height ?? '10em';
    $labelFor = $labelFor ?? $name . '_seleccionados';
    $optionValue = $optionValue ?? 'id';
@endphp

<div class="row mb-3">
    <div class="col-2">
        {{ html()->label(__($label), $labelFor)->class('form-label') }}
    </div>
    <div class="col">
        <label class="mb-1">{{ __('Selected') }}</label>
        <select id="{{ $nameId }}-select1"
                name="{{ $name }}[]"
                class="{{ $selectClass }}"
                multiple
            @style(['height:' . $height])>
            @foreach($selected as $item)
                @php
                    $value = is_callable($optionValue) ? $optionValue($item) : $item->{$optionValue};
                    $text = is_callable($optionText) ? $optionText($item) : $item->{$optionText};
                @endphp
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-1 d-flex flex-row justify-content-center align-items-center mt-3">
        <button data-selector="{{ $nameId }}" type="button" class="btn btn-primary btn-sm add">
            <i class="bi bi-arrow-left"></i>
        </button>
        <button data-selector="{{ $nameId }}" type="button" class="btn btn-primary btn-sm ms-1 remove">
            <i class="bi bi-arrow-right"></i>
        </button>
    </div>
    <div class="col">
        <label class="mb-1">{{ __('Available') }}</label>
        <select id="{{ $nameId }}-select2"
                class="{{ $selectClass }}"
                multiple
            @style(['height:' . $height])>
            @foreach($available as $item)
                @php
                    $value = is_callable($optionValue) ? $optionValue($item) : $item->{$optionValue};
                    $text = is_callable($optionText) ? $optionText($item) : $item->{$optionText};
                @endphp
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>
    </div>
</div>

