@component('mail::message')
# {{ __('Deadline extended') }}

{{ $usuario }}, se ha ampliado el plazo de la actividad "{{ $actividad }}".

@component('mail::button', ['url' => "https://$hostName/home"])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
