<div class="d-sm-flex d-inline-flex ms-2">
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        @if(LaravelLocalization::getCurrentLocale() != $localeCode)
            <li class="nav-link px-0 me-2 d-sm-inline-flex">
                <div class='btn-group'>
                    <a class="btn btn-outline-{{ $debug_text_color }} btn-sm p-0 text-center"
                       style="width:24px;height:24px;"
                       rel="alternate"
                       hreflang="{{ $localeCode }}" title="{{ $properties['native'] }}"
                       href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        <span class="text-uppercase" style="font-size:10px;line-height:24px;">{{ $localeCode }}</span>
                    </a>
                </div>
            </li>
        @endif
    @endforeach
</div>
