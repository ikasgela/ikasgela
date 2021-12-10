@component('mail::message')
# {{ __('Deadline extended') }}

{{ __(':user, the deadline of the activity :activity has been extended.', [
    'user' => $usuario,
    'activity' => $actividad,
]) }}

@component('mail::button', ['url' => "https://$hostName/home"])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
