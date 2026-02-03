@foreach($criteria_group->criterias as $criteria)
    <livewire:criteria-component
        :$criteria
        :key="'criteria-'.$criteria->id"
        :$rubric_is_editing
        :$rubric_is_qualifying
    />
@endforeach
@if($rubric_is_editing)
    <div class="col-auto align-content-center mb-3">
        <button class="btn btn-sm btn-success h-100"
                wire:click="add_criteria({{ $criteria_group->id }})">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>
@endif
