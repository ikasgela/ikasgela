<a class="dropdown-item" href="{{ route('users.toggle_help') }}"
   onclick="event.preventDefault(); document.getElementById('toggle_help').submit();">
    <span class="text-center ml-n2 me-1" style="width: 1.5rem;">
        @if(session('tutorial'))
            <i class="bi bi-check-lg text-success"></i>
        @else
            <i class="bi bi-x"></i>
        @endif
    </span> {{ __('View tutorial') }}
</a>
<form id="toggle_help" action="{{ route('users.toggle_help') }}" method="POST"
      style="display: none;">
    @csrf
</form>
