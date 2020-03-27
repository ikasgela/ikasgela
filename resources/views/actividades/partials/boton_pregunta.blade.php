<div class="d-inline-flex align-items-center">
    <span class="mr-2">{{ __('Need help?') }}</span>

    {!! Form::open(['route' => ['messages.create-with-subject'], 'method' => 'POST']) !!}

    {!! Form::button(__('Ask a question'), ['type' => 'submit', 'class' => 'btn btn-success']) !!}

    {!! Form::hidden('titulo', __('Activity').': '.$actividad->nombre) !!}

    {!! Form::close() !!}
</div>
