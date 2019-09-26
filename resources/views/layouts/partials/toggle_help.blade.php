<a class="dropdown-item" href="{{ route('users.toggle_help') }}"
   onclick="event.preventDefault(); document.getElementById('toggle_help').submit();">
    @if(session('tutorial'))
        <i class="fas fa-check text-success"></i>
    @else
        <i class="fas fa-times"></i>
    @endif
    {{ __('View tutorial') }}
</a>
<form id="toggle_help" action="{{ route('users.toggle_help') }}" method="POST"
      style="display: none;">
    @csrf
</form>
