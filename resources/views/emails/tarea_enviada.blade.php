@component('mail::message')
# {{ __('New submission received') }}

{{ $tarea->user->name }} ha enviado una nueva tarea para revisar.

@component('mail::button', ['url' => route('profesor.revisar', ['user' => $tarea->user->id, 'actividad'=>$tarea->id]) ])
Ver en el panel de control
@endcomponent

@endcomponent
