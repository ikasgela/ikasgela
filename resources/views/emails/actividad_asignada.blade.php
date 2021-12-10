@component('mail::message')
# {{ __('New activities assigned') }}

{{ $usuario }}, {{ __('there are new activities available in your account') }}.

@component('mail::panel')
### {{ __('Activities') }}
{{ $asignadas }}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/home"])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
