<ul class="c-header-nav ml-auto mr-3">
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <li class="px-1">
            <a rel="alternate" hreflang="{{ $localeCode }}"
               href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                <span class="flag-icon flag-icon-{{ $localeCode == 'en' ? 'gb' : $localeCode }}"></span>
            </a>
        </li>
    @endforeach
</ul>
