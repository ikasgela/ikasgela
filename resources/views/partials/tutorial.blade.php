@if(session('tutorial'))
    <div class="alert alert-{{ $color }}">
        <p class="m-0"><small class="fw-bold">{{ __('Tutorial') }}</small></p>
        <p class="m-0"><small class="">{{ $texto }}</small></p>
    </div>
@endif
