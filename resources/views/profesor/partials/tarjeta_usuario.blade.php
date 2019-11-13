<div class="flex-fill">
    {{-- Tarjeta --}}
    <div class="card m-0">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 p-3">
                    <h5 class="card-title">
                        <a title="{{ __('Edit') }}" href="{{ route('users.edit', [$user->id]) }}"
                           class="text-dark">{{ $user->name }}</a>
                        @include('profesor.partials.status_usuario')
                    </h5>
                    <a href="mailto:{{ $user->email }}" class="card-link">{{ $user->email }}</a>
                </div>
                <div class="col-12 col-sm-6 text-center text-sm-right">
                    <img style="width:100px" src="{{ $user->avatar_url(200)}}"
                         onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';">
                </div>
            </div>
        </div>
    </div>
    {{-- Fin tarjeta--}}
</div>
