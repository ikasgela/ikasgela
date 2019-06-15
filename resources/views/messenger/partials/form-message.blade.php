<h3>{{ __('New reply') }}</h3>

{!! Form::open(['route' => ['messages.update', $thread->id], 'method' => 'PUT']) !!}

<div class="form-group">
    <textarea rows="10" class="form-control" id="message" name="message">{!! old('message') !!}</textarea>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
</div>

@include('layouts.errors')
{!! Form::close() !!}

<div class="form-group">
    @include('partials.backbutton')
</div>
