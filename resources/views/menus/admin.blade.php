<li class="nav-title">{{ __('Admin') }}</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Structure') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('organizations.index') }}">
                {{ __('Organizations') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('periods.index') }}">
                {{ __('Periods') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('categories.index') }}">
                {{ __('Categories') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cursos.index') }}">
                {{ __('Courses') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('unidades.index') }}">
                {{ __('Units') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('actividades.index') }}">
                {{ __('Activities') }}
            </a>
        </li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Users') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('groups.index') }}">
                {{ __('Groups') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('teams.index') }}">
                {{ __('Teams') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users.index') }}">
                {{ __('Users') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('roles.index') }}">
                {{ __('Roles') }}
            </a>
        </li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-database"></i> {{ __('Evaluation') }}
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('qualifications.index') }}">
                {{ __('Qualifications') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('skills.index') }}">
                {{ __('Skills') }}
            </a>
        </li>
    </ul>
</li>
