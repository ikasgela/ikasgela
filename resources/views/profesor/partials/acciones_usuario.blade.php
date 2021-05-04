@if(Auth::user()?->hasRole('admin'))
    <div class='btn-group ml-3'>
        @include('users.partials.acciones')
    </div>
@endif

<div class='btn-group ml-3'>
    @if(Auth::user()?->hasRole('tutor'))
        {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-graduation-cap"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Results')
        ]) !!}
        {!! Form::hidden('user_id',$user->id) !!}
        {!! Form::close() !!}
    @endif
    @if(Auth::user()?->hasRole('profesor'))
        {!! Form::open(['route' => ['archivo.index'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-archive"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Archived')
        ]) !!}
        {!! Form::hidden('user_id',$user->id) !!}
        {!! Form::close() !!}
    @endif
    @if(Auth::user()?->hasRole('profesor'))
        {!! Form::open(['route' => ['messages.create-with-subject'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-envelope"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Message')
        ]) !!}
        {!! Form::hidden('user_id', $user->id) !!}
        {!! Form::close() !!}
    @endif
</div>
