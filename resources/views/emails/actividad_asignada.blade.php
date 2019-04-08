@component('mail::message')
# {{ __('New activities assigned') }}

{{ $usuario }}, hay nuevas actividades disponibles en tu cuenta.

@component('mail::panel')
### Actividades
{{ $asignadas }}
@endcomponent

@component('mail::button', ['url' => route('users.home')])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
