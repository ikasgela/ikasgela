<div class="form-group">
    <a href="{{ route('messages') }}" class="btn btn-secondary">{{ __('Back') }}</a>
</div>

<h3>{{ __('New reply') }}</h3>

{!! Form::open(['route' => ['messages.update', $thread->id], 'method' => 'PUT']) !!}

<div class="form-group">
    <textarea name="message" class="form-control" rows="5">{{ old('message') }}</textarea>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
</div>

@include('layouts.errors')
{!! Form::close() !!}
