@if(session('tutorial'))
    <div class="alert alert-{{ $color }}">
        <p class="m-0"><small class="fw-bold">{{ trans('tutorial.titular') }}</small></p>
        <p class="m-0"><small class="">{!! $texto !!}</small></p>
    </div>
@endif
