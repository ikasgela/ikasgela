@php
    $criterias = $criteria_group->criterias;
    $ordenes_criterias = $criterias->map(fn ($c) => $c->orden);
@endphp
@foreach($criterias as $i => $criteria)
    @php
        $orden_vecinos = sha1(($ordenes_criterias[$i - 1] ?? '') . '|' . ($ordenes_criterias[$i + 1] ?? ''));
    @endphp
    <livewire:criteria-component
        :$criteria
        :key="'criteria-'.$criteria->id"
        :orden_vecinos="$orden_vecinos"
        :$rubric_is_editing
        :$rubric_is_qualifying
    />
@endforeach
@if($rubric_is_editing)
    <div class="col-auto align-content-center mb-3">
        <button class="btn btn-sm btn-success h-100"
                title="{{ __('Add criteria') }}"
                wire:click="add_criteria({{ $criteria_group->id }})">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>
@endif
