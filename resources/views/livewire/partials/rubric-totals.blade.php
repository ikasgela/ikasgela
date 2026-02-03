@if($criteria_group->criterias->count() > 0)
    <div class="col-auto mb-3">
        @if($rubric_is_editing || !$this->is_rubric_completed)
            <button
                disabled
                class="btn opacity-100 btn-secondary p-3" style="min-width: 6em;">
                {{ $this->max_total }}
            </button>
        @else
            <button
                disabled
                class="btn opacity-100 btn-secondary p-3" style="min-width: 6em;">
                {{ $this->total }}/{{ $this->max_total }}
            </button>
        @endif
    </div>
@endif
