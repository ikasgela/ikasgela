{!! $user->isBlocked() ? '<span class="badge badge-danger ml-2">'.__('Blocked').'</span>' : '' !!}
{!! !$user->isVerified() ? '<span class="badge badge-warning ml-2">'.__('Unverified').'</span>' : '' !!}
@foreach($user->etiquetas() as $etiqueta)
    {!! '<a class="badge badge-secondary ml-2" href="'.route(explode('.',Route::currentRouteName())[0].'.index.filtro',['tag'=>$etiqueta]).'">'.$etiqueta.'</a>' !!}
@endforeach
{!! $user->baja_ansiedad ? '<span class="badge badge-secondary ml-2">'.__('Low anxiety').'</span>' : '' !!}
