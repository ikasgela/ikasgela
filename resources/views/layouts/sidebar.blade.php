@auth
    @if(Auth::user()->hasRole('alumno'))
        <ul class="nav nav-pills flex-column">
            @include('menus.alumno')
        </ul>
    @endif
    @if(Auth::user()->hasRole('profesor'))
        <ul class="nav nav-pills flex-column">
            @include('menus.profesor')
        </ul>
    @endif
    @if(Auth::user()->hasRole('tutor'))
        <ul class="nav nav-pills flex-column">
            @include('menus.tutor')
        </ul>
    @endif
    @if(Auth::user()->hasRole('admin'))
        <ul class="nav nav-pills flex-column">
            @include('menus.admin')
        </ul>
    @endif
    @if(Auth::user()->hasAnyRole(['admin', 'profesor', 'tutor', 'alumno']))
        <ul class="nav nav-pills flex-column">
            @include('menus.general')
        </ul>
    @endif
@endauth
