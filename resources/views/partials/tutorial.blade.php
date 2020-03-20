@if(session('tutorial'))
    <div class="c-callout {{ $color  }} b-t-1 b-r-1 b-b-1">
        <small class="text-muted">{{ __('Tutorial') }}</small>
        <p>{!! $texto !!}</p>
    </div>
@endif
