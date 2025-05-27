@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
    'success', 'error' => $level,
    default => 'primary',
};
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
@lang("If you’re having trouble clicking the button, copy and paste in your browser the URL below.")

<br><br>
<a href="{{ $actionUrl }}">{!! url($actionUrl) !!}</a>

@endcomponent
@endisset
@endcomponent
