@component('mail::message')
# {{ __('New submission received') }}

{{ $tarea->user->full_name }} {{ __('has sent a new task for review') }}.

@component('mail::button', ['url' => "https://$hostName/profesor/".$tarea->user->id."/revisar/".$tarea->id])
{{ __('View it on the control panel') }}
@endcomponent

@endcomponent
