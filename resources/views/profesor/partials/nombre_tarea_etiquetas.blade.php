<span class="mr-2">{{ $actividad->unidad->slug.'/'.$actividad->slug }}</span>
@foreach($actividad->etiquetas() as $etiqueta)
    {!! '<a class="badge badge-secondary ml-2" href="'.route(explode('.',Route::currentRouteName())[0].'.tareas.filtro',['user'=>$user->id, 'tag'=>$etiqueta]).'">'.$etiqueta.'</a>' !!}
@endforeach
