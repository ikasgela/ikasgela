@component('mail::message')
# {{ __('Export completed') }}

{{ __('The download link below is valid for 24 hours.') }}

@component('mail::button', ['url' => $url])
{{ __('Download the archive') }}
@endcomponent

{{ __('Unzip the file and open `index.html` file to view the table of contents.') }}

@endcomponent
