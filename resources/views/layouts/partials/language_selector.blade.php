<ul class="c-header-nav ml-auto me-3">
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        @if(LaravelLocalization::getCurrentLocale() != $localeCode)
            <li class="ps-2">
                <div class='btn-group p-0'>
                    <a class="btn btn-light btn-sm p-0 text-center" style="width:23px;height:23px;" rel="alternate"
                       hreflang="{{ $localeCode }}" title="{{ $properties['native'] }}"
                       href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        <span class="text-uppercase font-weight-bold"
                              style="font-size:10px;line-height:23px;">{{ $localeCode }}</span>
                    </a>
                </div>
            </li>
        @endif
    @endforeach
</ul>
