<li class="nav-item dropdown d-flex align-items-center">
    <button
        class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center text-{{ $debug_text_color }}"
        id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static"
        aria-label="Toggle theme (auto)">
        <i class="bi me-1 theme-icon-active"></i>
        <span class="d-md-none ms-2" id="bd-theme-text">{{ trans('theme-selector.title') }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
        <li>
            <button type="button" class="dropdown-item d-flex align-items-center"
                    data-bs-theme-value="light" aria-pressed="false">
                <i class="bi bi-sun-fill me-2 theme-icon" data-icon="bi-sun-fill"></i>
                <span>{{ trans('theme-selector.light') }}</span>
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item d-flex align-items-center"
                    data-bs-theme-value="dark" aria-pressed="false">
                <i class="bi bi-moon-stars-fill me-2 theme-icon" data-icon="bi-moon-stars-fill"></i>
                <span>{{ trans('theme-selector.dark') }}</span>
            </button>
        </li>
        <li>
            <button type="button" class="dropdown-item d-flex align-items-center active"
                    data-bs-theme-value="auto" aria-pressed="false">
                <i class="bi bi-circle-half me-2 theme-icon" data-icon="bi-circle-half"></i>
                <span>{{ trans('theme-selector.auto') }}</span>
            </button>
        </li>
    </ul>
</li>
