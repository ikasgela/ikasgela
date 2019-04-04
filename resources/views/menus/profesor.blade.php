<li class="nav-title">{{ __('Teacher') }}</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('users.index') }}">
        <i class="nav-icon fas fa-tasks"></i> {{ __('Assign activities') }}
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('actividades.plantillas') }}">
        <i class="nav-icon fas fa-file"></i> {{ __('Activity templates') }}
    </a>
</li>

{{--
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="nav-icon icon-pencil"></i> Actividades
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="nav-icon icon-lock"></i> Repositorios
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="nav-icon icon-settings"></i> Seguimiento de alumnos
    </a>
</li>
<li class="nav-title">Administrador</li>

<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="nav-icon icon-pencil"></i> Estructura de centros
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#">
        <i class="nav-icon icon-lock"></i> Usuarios
    </a>
</li>
--}}
