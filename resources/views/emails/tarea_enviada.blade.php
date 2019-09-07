@component('mail::message')
# {{ __('New submission received') }}

{{ $tarea->user->name }} ha enviado una nueva tarea para revisar.

@component('mail::button', ['url' => "https://$hostName/profesor/".$tarea->user->id."/revisar/".$tarea->id])
Ver en el panel de control
@endcomponent

@endcomponent
