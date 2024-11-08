<div class="d-flex align-items-center">
    @isset($slug)
        <span>{{ $actividad->unidad->slug.'/'.$actividad->slug }}</span>
    @else
        <span>{{ $actividad->nombre }}</span>
    @endif
    @foreach($actividad->etiquetas() as $etiqueta)
        @isset($ruta)
            {!! '<a class="badge bg-body-secondary text-body-secondary ' . ($loop->first ? 'ms-2' : 'ms-1') . '" href="'
                    . route($ruta, ['user' => $user->id ?? null, 'tag_actividad' => $etiqueta])
                    . '">' . $etiqueta . '</a>' !!}
        @else
            {!! '<span class="badge bg-body-secondary text-body-secondary ' . ($loop->first ? 'ms-2' : 'ms-1') . '">' . $etiqueta . '</span>' !!}
        @endisset
    @endforeach
</div>
