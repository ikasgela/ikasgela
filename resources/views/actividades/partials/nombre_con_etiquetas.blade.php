@isset($slug)
    <span class="mr-2">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</span>
@else
    <span class="mr-2">{{ $actividad->nombre }}</span>
@endif
@foreach($actividad->etiquetas() as $etiqueta)
    @isset($ruta)
        {!! '<a class="badge badge-secondary mr-2" href="'
                . route($ruta, ['user' => $user->id ?? null, 'tag_actividad' => $etiqueta])
                . '">' . $etiqueta . '</a>' !!}
    @else
        {!! '<span class="badge badge-secondary mr-2">' . $etiqueta . '</span>' !!}
    @endisset
@endforeach
