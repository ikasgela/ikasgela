@php( $class = $thread->isUnread(Auth::id()) ? 'alert-success' : '' )

<div class="card {{ $class }}">
    <div class="card-header d-flex justify-content-between">
        <span><i class="fas fa-comment"></i> {{ $thread->participantsString(Auth::id()) }}</span>
        <span>{{ $thread->userUnreadMessagesCount(Auth::id()) }} {{ __('unread') }}</span>
    </div>
    <div class="card-body pb-1">
        <h5 class="card-title"><a href="{{ route('messages.show', $thread->id) }}">{{ $thread->subject }}</a></h5>
        <small class="text-muted">{{ __('Latest message') }}</small>
        <div class="callout callout-primary bg-light py-3">
            {!! $thread->latestMessage->body !!}
        </div>
    </div>
</div>