@component('mail::message')
# {{ __('Export completed') }}

{{ __('The export is complete.') }}

{{ __('The download link below is valid for 24 hours.') }}

@component('mail::button', ['url' => $url])
{{ __('Download the archive') }}
@endcomponent

@endcomponent
