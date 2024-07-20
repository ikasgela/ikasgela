{!! $user->isBlocked() ? '<span class="badge badge-danger ms-2">'.__('Blocked').'</span>' : '' !!}
{!! !$user->isVerified() ? '<span class="badge badge-warning ms-2">'.__('Unverified').'</span>' : '' !!}
