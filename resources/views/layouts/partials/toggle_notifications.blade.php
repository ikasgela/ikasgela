<a class="dropdown-item" href="{{ route('users.toggle_notifications') }}"
   onclick="event.preventDefault(); document.getElementById('toggle_notifications').submit();">
    @if(session('enviar_emails'))
        <i class="fas fa-check text-success"></i>
    @else
        <i class="fas fa-times"></i>
    @endif
    {{ __('Send email notifications') }}
</a>
<form id="toggle_notifications" action="{{ route('users.toggle_notifications') }}" method="POST"
      style="display: none;">
    @csrf
</form>
