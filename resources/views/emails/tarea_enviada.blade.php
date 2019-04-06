@component('mail::message')
# {{ __('New submission received') }}

{{ $tarea->user->name }} ha enviado una nueva tarea para revisar.

@component('mail::button', ['url' => route('archivo.show', $tarea->actividad->id)])
Ver en el panel de control
@endcomponent

@endcomponent
