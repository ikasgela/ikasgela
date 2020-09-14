@php($count = \App\Hilo::forUserWithNewMessages(Auth::id())->cursoActual()->count())
@if($count > 0)
    {{ $count }}
@endif
