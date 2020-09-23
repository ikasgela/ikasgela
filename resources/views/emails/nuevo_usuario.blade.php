@component('mail::message')
# {{ __('New user registered') }}

Hay un nuevo usuario registrado: {{ $usuario->name }} {{ $usuario->surname }}.

@component('mail::button', ['url' => "https://$hostName/alumnos"])
    {{ __('Go to desktop') }}
@endcomponent

@endcomponent
