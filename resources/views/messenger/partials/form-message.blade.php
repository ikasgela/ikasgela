<h3>{{ __('New reply') }}</h3>

{{ html()->form('PUT', route('messages.update', $thread->id))->open() }}

<div class="input-group mb-3">
    <textarea rows="10" class="form-control" id="message" name="message">{!! old('message') !!}</textarea>
</div>

<div class="input-group mb-3">
    @include('partials.guardar_cancelar',['texto' => __('Send')])
</div>

@include('layouts.errors')
{{ html()->form()->close() }}
