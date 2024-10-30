@if(Auth::user()?->hasRole('admin'))
    <div class='btn-group ms-3'>
        @include('users.partials.acciones')
    </div>
@endif

@if(Auth::user()?->hasAnyRole(['profesor', 'admin']))
    <div class='btn-group ms-3'>
        <a title="{{ __('Control panel') }}"
           href="{{ route('profesor.tareas', ['user' => $user->id]) }}"
           class='btn btn-light btn-sm'><i class="fas fa-tasks"></i></a>

        {{ html()->form('POST', route('results.alumno'))->open() }}
        {{ html()->submit('<i class="fas fa-graduation-cap"></i>')->class(['btn btn-light btn-sm', 'rounded-0'])->attribute('title', __('Results')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}

        {{ html()->form('POST', route('archivo.index'))->open() }}
        {{ html()->submit('<i class="fas fa-archive"></i>')->class(['btn btn-light btn-sm', 'rounded-0'])->attribute('title', __('Archived')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}

        {{ html()->form('POST', route('messages.create-with-subject'))->open() }}
        {{ html()->submit('<i class="fas fa-envelope"></i>')->class(['btn btn-light btn-sm', 'rounded-start-0'])->attribute('title', __('Message')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}
    </div>
@endif
