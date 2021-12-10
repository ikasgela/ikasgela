@component('mail::message')
# {{ __('New user registered') }}

{{ __('There is a new registered user') }}: {{ $usuario->full_name }}.

@component('mail::button', ['url' => "https://$hostName/alumnos"])
    {{ __('Go to desktop') }}
@endcomponent

@endcomponent
