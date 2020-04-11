<div class='btn-group ml-3'>
    <a title="{{ __('Edit') }}" href="{{ route('users.edit', [$user->id]) }}"
       class='btn btn-light btn-sm'><i class="fas fa-pen"></i></a>

    {!! Form::open(['route' => ['results.alumno'], 'method' => 'POST']) !!}
    {!! Form::button('<i class="fas fa-graduation-cap"></i>', ['type' => 'submit',
        'class' => 'btn btn-light btn-sm', 'title' => __('Results')
    ]) !!}
    {!! Form::hidden('user_id',$user->id) !!}
    {!! Form::close() !!}

    {!! Form::open(['route' => ['messages.create-with-subject'], 'method' => 'POST']) !!}
    {!! Form::button('<i class="fas fa-envelope"></i>', ['type' => 'submit',
        'class' => 'btn btn-light btn-sm', 'title' => __('Message')
    ]) !!}
    {!! Form::hidden('user_id', $user->id) !!}
    {!! Form::close() !!}

    @include('users.partials.impersonate_button')
</div>
