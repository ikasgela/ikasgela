@component('mail::message')
# {{ __('New message') }}

Has recibido un nuevo mensaje.

@component('mail::panel')
    {!! $preview_mensaje !!}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/messages"])
    {{ __('Open messages') }}
@endcomponent

@endcomponent
