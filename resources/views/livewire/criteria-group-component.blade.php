<div class="card-body pb-0">
    <div class="row">
        <div class="col-2 mb-3">
            @include('livewire.partials.criteria-group-header')
        </div>
        <div class="col overflow-x-auto">
            <div class="row h-100 flex-nowrap">
                @include('livewire.partials.criteria-group-body')
            </div>
        </div>
        @include('livewire.partials.criteria-group-botones')
        @include('livewire.partials.rubric-totals')
    </div>
</div>
