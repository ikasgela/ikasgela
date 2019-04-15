@component('mail::message')
# {{ __('New message received') }}

Has recibido un nuevo mensaje.

@component('mail::button', ['url' => route('messages')])
    {{ __('Open messages') }}
@endcomponent

@endcomponent
