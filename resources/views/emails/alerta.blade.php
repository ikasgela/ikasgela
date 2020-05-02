@component('mail::message')
# ⚠️ {{ __('Important notice') }}

@component('mail::panel')
## {{ $titulo }}
{!! $preview !!}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/messages"])
{{ __('Open messages') }}
@endcomponent

@endcomponent
