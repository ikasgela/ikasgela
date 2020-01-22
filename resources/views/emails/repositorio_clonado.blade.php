@component('mail::message')
# {{ __('Repository cloned') }}

{{ __('The cloning is complete.') }}

@component('mail::button', ['url' => "https://$hostName/home"])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
