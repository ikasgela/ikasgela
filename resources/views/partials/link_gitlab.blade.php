@isset($proyecto['http_url_to_repo'])
    <a target="_blank" href="{{ $proyecto['http_url_to_repo'] }}">{{ $proyecto['path_with_namespace'] }}</a>
@else
    <a target="_blank" href="{{ $proyecto['html_url'] }}">{{ $proyecto['name'] }}</a>
@endisset
