@if(Auth::user()?->hasRole('admin'))
    <div class='btn-group ms-3'>
        @include('users.partials.acciones')
    </div>
@endif

@if(Auth::user()?->hasAnyRole(['profesor', 'admin']))
    <div class='btn-group ms-3'>
        <a title="{{ __('Control panel') }}"
           href="{{ route('profesor.tareas', ['user' => $user->id]) }}"
           class='btn btn-light btn-sm'><i class="bi bi-person-gear"></i></a>

        {{ html()->form('POST', route('archivo.descargar'))->open() }}
        {{ html()->submit('<i class="bi bi-download"></i>')->class(['btn btn-light btn-sm', 'rounded-0'])->attribute('title', __('Download user projects')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}

        {{ html()->form('POST', route('results.alumno'))->open() }}
        {{ html()->submit('<i class="bi bi-mortarboard"></i>')->class(['btn btn-light btn-sm', 'rounded-0'])->attribute('title', __('Results')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}

        {{ html()->form('POST', route('archivo.index'))->open() }}
        {{ html()->submit('<i class="bi bi-archive"></i>')->class(['btn btn-light btn-sm', 'rounded-0'])->attribute('title', __('Archived')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}

        {{ html()->form('POST', route('messages.create-with-subject'))->open() }}
        {{ html()->submit('<i class="bi bi-chat-text"></i>')->class(['btn btn-light btn-sm', 'rounded-start-0'])->attribute('title', __('Message')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}
    </div>
@endif
