@component('mail::message')
# {{ __('Repository cloning error') }}

{{ __('There was an error cloning the repository, contact your administrator.') }}

@component('mail::button', ['url' => "https://$hostName/home"])
{{ __('Go to desktop') }}
@endcomponent

@endcomponent
