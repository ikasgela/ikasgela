@if($user->canBeImpersonated())
    <a title="{{ __('Impersonate') }}"
       href="{{ route('impersonate', $user->id) }}"
       class='btn btn-light btn-sm'><i class="bi bi-incognito"></i></a>
@endif
