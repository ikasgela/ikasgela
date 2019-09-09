{!! $user->isBlocked() ? '<span class="badge badge-secondary ml-2">'.__('Blocked').'</span>' : '' !!}
{!! !$user->isVerified() ? '<span class="badge badge-secondary ml-2">'.__('Unverified').'</span>' : '' !!}
