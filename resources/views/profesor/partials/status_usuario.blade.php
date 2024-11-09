{!! $user->isBlocked() ? '<span class="badge text-bg-danger ms-2">'.__('Blocked').'</span>' : '' !!}
{!! !$user->isVerified() ? '<span class="badge text-bg-warning ms-2">'.__('Unverified').'</span>' : '' !!}
