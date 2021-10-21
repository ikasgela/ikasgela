@if(Auth::user()?->hasRole('admin'))
    <div class='btn-group ml-3'>
        @include('users.partials.acciones')
    </div>
@endif

@if(Auth::user()?->hasAnyRole(['profesor', 'admin']))
    <div class='btn-group ml-3'>
        <a title="{{ __('Control panel') }}"
           href="{{ route('profesor.tareas', ['user' => $user->id]) }}"
           class='btn btn-light btn-sm'><i class="fas fa-tasks"></i></a>

        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-graduation-cap"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Results')
        ]) !!}
        {!! Form::hidden('user_id',$user->id) !!}
        {!! Form::close() !!}

        {!! Form::open(['route' => ['archivo.index'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-archive"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Archived')
        ]) !!}
        {!! Form::hidden('user_id',$user->id) !!}
        {!! Form::close() !!}

        {!! Form::open(['route' => ['messages.create-with-subject'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-envelope"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Message')
        ]) !!}
        {!! Form::hidden('user_id', $user->id) !!}
        {!! Form::close() !!}
    </div>
@endif
