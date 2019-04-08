<div class="flex-fill">
    {{-- Tarjeta --}}
    <div class="card m-0">
        <div class="card-body">
            <div class="d-flex flex-row">
                <div class="flex-fill">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <a href="mailto:{{ $user->email }}" class="card-link">{{ $user->email }}</a>
                </div>
                <div>
                    <img style="width:100px;" src="{{ $user->avatar_url()}}">
                </div>
            </div>
        </div>
    </div>
    {{-- Fin tarjeta--}}
</div>
