@php( $class = !$thread->alert && $thread->isUnread(Auth::id()) ? 'bg-success text-white' : '' )

<div class="card">
    <div class="card-header d-flex justify-content-between {{ $class }} {{ $thread->alert ? 'bg-warning' : '' }}">
        <span>
            @if(!$thread->alert)
                <i class="fas fa-comment"></i>
            @else
                <i class="fas fa-exclamation-triangle"></i>
            @endif
            <span class="ml-2">
                {{ $thread->creator()->name }}
            </span>
        </span>
        <span>{{ $thread->userUnreadMessagesCount(Auth::id()) }} {{ __('unread') }}</span>
    </div>
    <div class="card-body pb-1">
        <div class="d-flex justify-content-between">
            <h5 class="card-title"><a href="{{ route('messages.show', $thread->id) }}">{{ $thread->subject }}</a></h5>
            @auth
                @if(Auth::user()->hasRole('profesor'))
                    {!! Form::open(['route' => ['messages.destroy', $thread->id], 'method' => 'DELETE']) !!}
                    <div class="btn-group">
                        @include('partials.boton_borrar')
                    </div>
                    {!! Form::close() !!}
                @endif
            @endauth
        </div>
        <small class="text-muted">{{ __('Latest message') }}</small>
    </div>
    <div class="media rounded bg-light m-3 line-numbers">
        <div class="media-body px-3 pt-3 overflow-auto" style="border-left: 4px solid #c8ced3; border-radius: 0.25em">
            {!! $thread->latestMessage->body !!}
        </div>
    </div>
</div>
