<div class='btn-group'>
    @include('users.partials.impersonate_button')

    @if(!$user->hasVerifiedEmail())
        {{ html()->form('POST', route('users.manual_activation'))->open() }}
        {{ html()->submit('<i class="bi bi-person-check-fill"></i>')
                ->class(['btn btn-light btn-sm', 'rounded-end-0'])
                ->attribute('title', __('Manual verification')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}
    @endif

    {{ html()->form('POST', route('profesor.index.filtro'))->open() }}
    {{ html()->submit('<i class="bi bi-filter"></i>')
            ->class(['btn btn-sm',
                     session('filtrar_user_actual') == $user->id ? 'btn-primary' : 'btn-light',
                     $user->canBeImpersonated() || !$user->hasVerifiedEmail() ? 'rounded-0' : 'rounded-end-0'
            ])
            ->attribute('title', __('Filter')) }}
    {{ html()->hidden('user_id', $user->id) }}
    {{ html()->form()->close() }}

    <a title="{{ __('Edit') }}"
       href="{{ route('users.edit', [$user->id]) }}"
       class='btn btn-light btn-sm'><i class="bi bi-pencil-square"></i></a>

    <a title="{{ __('Change password') }}"
       href="{{ route('users.password', [$user->id]) }}"
       class='btn btn-light btn-sm'><i class="bi bi-key"></i></a>

    {{ html()->form('POST', route('users.toggle_blocked'))->open() }}
    {{ html()->submit(!$user->isBlocked() ? '<i class="bi bi-unlock2"></i>':'<i class="bi bi-lock"></i>')
            ->class(['btn btn-light btn-sm', Route::currentRouteName() == 'users.index' ? 'rounded-0' : 'rounded-start-0'])
            ->attribute('title', !$user->isBlocked() ? __('Block') : __('Unblock')) }}
    {{ html()->hidden('user_id', $user->id) }}
    {{ html()->form()->close() }}

    @if(Route::currentRouteName() == 'users.index')
        {{ html()->form('DELETE', route('users.destroy', [$user->id]))->open() }}
        @include('partials.boton_borrar', ['last' => true])
        {{ html()->form()->close() }}
    @endif
</div>
