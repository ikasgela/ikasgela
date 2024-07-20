<li class="nav-item {{ isset($last) ? 'mb-2' : '' }}">
    <a href="{{ $route }}"
       @isset($target)
           target="{{ $target }}"
       @endisset
       class="nav-link text-light hover-background{{ request()->fullUrlIs($route) ? '-active' : '' }}">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @isset($icon)
                    <i class="bi {{ $icon }} me-2"></i>
                @endisset
                <span>{{ $text }}</span>
            </div>
            @isset($badge_number)
                @if($badge_number > 0)
                    <div class="badge text-bg-{{ $badge_color ?? 'primary' }} text-light fw-light">
                        {{ $badge_number }}
                    </div>
                @endif
            @endisset
        </div>
    </a>
</li>
