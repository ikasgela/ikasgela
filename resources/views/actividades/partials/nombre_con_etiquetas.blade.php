@isset($slug)
    <span class="me-2">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</span>
@else
    <span class="me-2">{{ $actividad->nombre }}</span>
@endif
@foreach($actividad->etiquetas() as $etiqueta)
    @isset($ruta)
        {!! '<a class="badge bg-body-secondary text-body-secondary" href="'
                . route($ruta, ['user' => $user->id ?? null, 'tag_actividad' => $etiqueta])
                . '">' . $etiqueta . '</a>' !!}
    @else
        {!! '<span class="badge bg-body-secondary text-body-secondary">' . $etiqueta . '</span>' !!}
    @endisset
@endforeach
