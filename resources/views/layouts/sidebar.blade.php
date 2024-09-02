<div class="d-flex flex-column p-3 text-bg-dark col-12 col-sm-2">
    @auth
        <ul class="nav nav-pills flex-column mb-auto">
            @if(Auth::user()->hasRole('alumno'))
                @include('menus.alumno')
            @endif
            @if(Auth::user()->hasRole('profesor'))
                @include('menus.profesor')
            @endif
            @if(Auth::user()->hasRole('tutor'))
                @include('menus.tutor')
            @endif
            @if(Auth::user()->hasAnyRole(['admin', 'profesor', 'tutor']))
                @include('menus.utilidades')
            @endif
            @if(Auth::user()->hasRole('admin'))
                @include('menus.admin')
            @endif
        </ul>
    @endauth
</div>
