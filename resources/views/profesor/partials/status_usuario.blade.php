{!! $user->isBlocked() ? '<span class="badge badge-danger ml-2">'.__('Blocked').'</span>' : '' !!}
{!! !$user->isVerified() ? '<span class="badge badge-warning ml-2">'.__('Unverified').'</span>' : '' !!}
