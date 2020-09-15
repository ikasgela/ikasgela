<div class='btn-group'>
    @if(!$user->hasVerifiedEmail())
        {!! Form::open(['route' => ['users.manual_activation'], 'method' => 'POST']) !!}
        {!! Form::button('<i class="fas fa-user-check"></i>', ['type' => 'submit',
            'class' => 'btn btn-light btn-sm', 'title' => __('Manual verification')
        ]) !!}
        {!! Form::hidden('user_id', $user->id) !!}
        {!! Form::close() !!}
    @endif
    @include('users.partials.impersonate_button')
    <a title="{{ __('Edit') }}"
       href="{{ route('users.edit', [$user->id]) }}"
       class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>
    <form method="POST" action="{{ route('users.destroy', [$user->id]) }}">
        @csrf
        @method('DELETE')
        @include('partials.boton_borrar')
    </form>
</div>
