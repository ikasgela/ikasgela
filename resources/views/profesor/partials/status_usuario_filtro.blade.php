{!! $user->isBlocked() ? '<span class="badge badge-secondary ml-2">'.__('Blocked').'</span>' : '' !!}
{!! !$user->isVerified() ? '<span class="badge badge-secondary ml-2">'.__('Unverified').'</span>' : '' !!}
@foreach($user->etiquetas() as $etiqueta)
    {!! '<a class="badge badge-secondary ml-2" href="'.route('profesor.index.filtro',['tag'=>$etiqueta]).'">'.$etiqueta.'</a>' !!}
@endforeach
