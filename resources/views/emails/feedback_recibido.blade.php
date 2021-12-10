@component('mail::message')
# {{ __('Review completed') }}

{{ __(':user, your submission of the activity :activity it is now reviewed.', [
    'user' => $tarea->user->name,
    'activity' => $tarea->actividad->nombre,
]) }}

@component('mail::panel')
### {{ __('Feedback') }}
{!! $tarea->feedback !!}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/home"])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
