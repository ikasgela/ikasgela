@component('mail::message')
# {{ __('Important notice') }}

@component('mail::panel')
    {!! $preview_mensaje !!}
@endcomponent

@component('mail::button', ['url' => "https://$hostName/messages"])
    {{ __('Open messages') }}
@endcomponent

@endcomponent
