<div class="media border rounded p-3 mb-3 bg-white">
    @include('users.partials.avatar', ['user' => $message->user, 'width' => 64])
    <div class="media-body ps-3 overflow-auto">
        <h5 class="media-heading">
            <div class="d-flex justify-content-between">
                <span>{{ $message->user?->name ?: __('Unknown user') }} {{ $message->user?->surname }}</span>
                <div class="btn-group">
                    @if(Auth::user()->hasRole('profesor') && $message->user?->hasRole('alumno'))
                        <a title="{{ __('Control panel') }}" target="_blank"
                           href="{{ route('profesor.tareas', ['user' => $message->user->id]) }}"
                           class='btn btn-light btn-sm'><i class="fas fa-tasks"></i></a>
                    @endif
                    @if(Auth::user()->hasRole('profesor'))
                        {!! Form::open(['route' => ['messages.destroy_message', $message->id], 'method' => 'DELETE']) !!}
                        @include('partials.boton_borrar')
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </h5>
        <div class="c-callout secondary bg-light py-3 line-numbers">
            {!! links_galeria($message->body, $message->thread->id) !!}
        </div>
        <div class="text-muted">
            <small title="{{ $message->created_at->isoFormat('dddd, LL LTS') }}">
                {{ __('Posted') }} {{ $message->created_at->diffForHumans() }}
            </small>
        </div>
    </div>
</div>
