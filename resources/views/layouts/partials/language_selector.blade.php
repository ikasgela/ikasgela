<ul class="c-header-nav ml-auto mr-3">
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        @if(LaravelLocalization::getCurrentLocale() != $localeCode)
            <li class="pl-2">
                <a rel="alternate" hreflang="{{ $localeCode }}" title="{{ $properties['native'] }}"
                   href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                    <span class="flag-icon flag-icon-{{ $localeCode == 'en' ? 'gb' : $localeCode }}"></span>
                </a>
            </li>
        @endif
    @endforeach
</ul>
