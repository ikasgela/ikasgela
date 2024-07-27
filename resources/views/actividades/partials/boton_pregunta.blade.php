<div class="d-inline-flex align-items-center">
    <span class="me-2">{{ __('Need help?') }}</span>

    {{ html()->form('POST', route('messages.create-with-subject'))->open() }}

    {{ html()->submit(__('Ask a question'))->class('btn btn-success text-light') }}

    {{ html()->hidden('titulo', __('Activity') . ': ' . $actividad->pretty_name) }}

    {{ html()->form()->close() }}
</div>
