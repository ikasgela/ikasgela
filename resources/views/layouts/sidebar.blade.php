<div class="d-flex flex-column p-3 text-bg-dark col-12 col-sm-2">
    @auth
        <ul class="nav nav-pills flex-column mb-auto">
            @if(Auth::user()->hasRole('alumno'))
                @include('menus.alumno')
            @endif
            @if(Auth::user()->hasRole('profesor'))
                @include('menus.profesor')
            @endif
        </ul>
    @endauth
</div>
