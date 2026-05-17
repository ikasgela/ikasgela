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

<script>
(function () {
    // Restaurar el estado de los dropdowns del sidebar al cargar la página.
    // El script se ejecuta inline para que las clases se apliquen antes de que
    // Bootstrap inicialice los collapses, evitando cualquier parpadeo visual.
    document.querySelectorAll('#sidebar .collapse[id]').forEach(function (el) {
        if (localStorage.getItem('sidebar:' + el.id) === '1') {
            el.classList.add('show');
            var btn = document.querySelector('[data-bs-target="#' + el.id + '"]');
            if (btn) btn.classList.remove('collapsed');
        }
    });

    document.addEventListener('show.bs.collapse', function (e) {
        if (e.target.id !== 'sidebar' && e.target.closest('#sidebar')) {
            localStorage.setItem('sidebar:' + e.target.id, '1');
        }
    });

    document.addEventListener('hide.bs.collapse', function (e) {
        if (e.target.id !== 'sidebar' && e.target.closest('#sidebar')) {
            localStorage.setItem('sidebar:' + e.target.id, '0');
        }
    });
})();
</script>
