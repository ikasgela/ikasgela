<div class="card ps-3 pt-3 my-3">
    @if(config('ikasgela.avatar_enabled'))
        <div class="d-flex align-items-start">
            <div>
                @include('users.partials.avatar', ['user' => $message->user, 'width' => 64])
            </div>
            @endif
            <div class="card-body pt-0">
                <div class="d-flex justify-content-between mb-3">
                    <h5>{{ $message->user?->name ?: __('Unknown user') }} {{ $message->user?->surname }}</h5>
                    <div class="btn-group">
                        @if(Auth::user()->hasRole('profesor') && $message->user?->hasRole('alumno'))
                            <a title="{{ __('Control panel') }}" target="_blank"
                               href="{{ route('profesor.tareas', ['user' => $message->user->id]) }}"
                               class='btn btn-light btn-sm'><i class="bi bi-person-gear"></i></a>
                        @endif
                        @if(Auth::user()->hasRole('profesor'))
                            {{ html()->form('DELETE', route('messages.destroy_message', $message->id))->open() }}
                            @include('partials.boton_borrar', [ 'last' => $message->user?->hasRole('alumno')])
                            {{ html()->form()->close() }}
                        @endif
                    </div>
                </div>
                <div class="text-body bg-light-subtle line-numbers mb-3">
                    <div class="px-3 pt-3 overflow-auto border-start border-secondary-subtle border-4">
                        {!! links_galeria($message->body, $message->thread->id) !!}
                    </div>
                </div>
                <small class="text-secondary" title="{{ $message->created_at->isoFormat('dddd, LL LTS') }}">
                    {{ __('Posted') }} {{ $message->created_at->diffForHumans() }}
                </small>
            </div>
            @if(config('ikasgela.avatar_enabled'))
        </div>
    @endif
</div>
