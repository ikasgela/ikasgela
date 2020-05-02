@component('mail::message')
# {{ __('New message') }}

Has recibido un nuevo mensaje de {{ $usuario }}.

@component('mail::panel')
## {{ $titulo }}
{!! $preview !!}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/messages"])
{{ __('Open messages') }}
@endcomponent

@endcomponent
