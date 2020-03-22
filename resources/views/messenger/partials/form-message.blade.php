<h3>{{ __('New reply') }}</h3>

{!! Form::open(['route' => ['messages.update', $thread->id], 'method' => 'PUT', 'id' => 'nuevo_mensaje']) !!}

<div class="form-group">
    <textarea rows="10" class="form-control" id="message" name="message">{!! old('message') !!}</textarea>
</div>

<div class="form-group">
    @include('partials.enviar_cancelar')
</div>

@include('layouts.errors')
{!! Form::close() !!}

