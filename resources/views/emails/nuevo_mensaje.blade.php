@component('mail::message')
# {{ __('New message') }}

{{ __('You have received a new message from :user.', ['user' => $usuario->full_name]) }}

@component('mail::panel')
## {{ $titulo }}
{!! $preview !!}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/messages"])
{{ __('Open messages') }}
@endcomponent

@endcomponent
