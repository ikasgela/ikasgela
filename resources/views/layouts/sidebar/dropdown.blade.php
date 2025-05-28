<button
    class="btn btn-toggle dropdown-toggle px-3 nav-link collapsed text-light w-100 text-start hover-background"
    data-bs-toggle="collapse"
    data-bs-target="#{{ $collapse_id }}">
    @isset($icon)
        <i class="bi {{ $icon }} me-2"></i>
    @endisset
    <span>{{ $text }}</span>
</button>
