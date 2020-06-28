<div class="media border rounded p-3 mb-3 bg-white">
    <img width="64" src="{{ $message->user->avatar_url(128) }}" alt="{{ $message->user->name }}"
         onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';">
    <div class="media-body pl-3 overflow-auto">
        <h5 class="media-heading">
            <span>{{ $message->user->name }}</span>
            @if(Auth::user()->hasRole('profesor') && $message->user->hasRole('alumno'))
                <a title="{{ __('Control panel') }}" target="_blank"
                   href="{{ route('profesor.tareas', ['user' => $message->user->id]) }}"
                   class='btn btn-light btn-sm ml-3'><i class="fas fa-tasks"></i></a>
            @endif
        </h5>
        <div class="c-callout c-callout-secondary bg-light py-3 line-numbers">
            {!! $message->body !!}
        </div>
        <div class="text-muted">
            <small>{{ __('Posted') }} {{ $message->created_at->diffForHumans() }}</small>
        </div>
    </div>
</div>
