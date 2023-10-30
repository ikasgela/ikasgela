@foreach($user->etiquetas() as $etiqueta)
    {!! '<a class="badge badge-secondary ml-2" href="'.route(explode('.',Route::currentRouteName())[0].'.index.filtro',['tag'=>$etiqueta]).'">'.$etiqueta.'</a>' !!}
@endforeach
