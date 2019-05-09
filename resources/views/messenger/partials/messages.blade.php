<div class="media border rounded p-3 mb-3 bg-white">
    <img width="64" src="//www.gravatar.com/avatar/{{ md5($message->user->email) }}?s=128"
         alt="{{ $message->user->name }}" class="img-circle">
    <div class="media-body pl-3">
        <h5 class="media-heading">{{ $message->user->name }}</h5>
        <pre>{{ $message->body }}</pre>
        <div class="text-muted">
            <small>{{ __('Posted') }} {{ $message->created_at->diffForHumans() }}</small>
        </div>
    </div>
</div>
