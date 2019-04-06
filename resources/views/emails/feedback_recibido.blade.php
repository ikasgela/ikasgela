@component('mail::message')
# {{ __('Review completed') }}

{{ $tarea->user->name }}, tu envío está revisado.

@component('mail::panel')
### Feedback
{{ $tarea->feedback }}
@endcomponent

@component('mail::button', ['url' => route('users.home')])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
