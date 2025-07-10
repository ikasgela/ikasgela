<div class='btn-group'>
    @include('users.partials.impersonate_button')

    @if(!$user->hasVerifiedEmail())
        {{ html()->form('POST', route('users.manual_activation'))->open() }}
        {{ html()->submit('<i class="fas fa-user-check"></i>')
                ->class(['btn btn-light btn-sm', 'rounded-0'])
                ->attribute('title', __('Manual verification')) }}
        {{ html()->hidden('user_id', $user->id) }}
        {{ html()->form()->close() }}
    @endif

    <a title="{{ __('Edit') }}"
       href="{{ route('users.edit', [$user->id]) }}"
       class='btn btn-light btn-sm'><i class="fas fa-edit"></i></a>

    <a title="{{ __('Change password') }}"
       href="{{ route('users.password', [$user->id]) }}"
       class='btn btn-light btn-sm'><i class="fas fa-key"></i></a>

    {{ html()->form('POST', route('users.toggle_blocked'))->open() }}
    {{ html()->submit(!$user->isBlocked() ? '<i class="fas fa-unlock"></i>':'<i class="fas fa-lock"></i>')
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
