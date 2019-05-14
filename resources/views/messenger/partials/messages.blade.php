<div class="media border rounded p-3 mb-3 bg-white">
    <img width="64" src="{{ $message->user->avatar_url(128) }}" alt="{{ $message->user->name }}" class="img-circle">
    <div class="media-body pl-3 overflow-auto">
        <h5 class="media-heading">{{ $message->user->name }}</h5>
        <div class="callout callout-primary bg-light py-3">
            {!! $message->body !!}
        </div>
        <div class="text-muted">
            <small>{{ __('Posted') }} {{ $message->created_at->diffForHumans() }}</small>
        </div>
    </div>
</div>
