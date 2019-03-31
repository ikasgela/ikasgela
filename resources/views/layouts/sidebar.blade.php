<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            @auth
                @if(Auth::user()->hasRole('alumno'))
                    <li class="nav-title">{{ __('Student') }}</li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.home') }}">
                            <i class="nav-icon icon-screen-desktop"></i> {{ __('Desktop') }}
                        </a>
                    </li>
                    {{--
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('users.dashboard') }}">
                                                <i class="nav-icon icon-graduation"></i> {{ __('Results') }}
                                            </a>
                                        </li>
                    --}}
                @endif
            @endauth
            @auth
                @if(Auth::user()->hasRole('profesor'))
                    <li class="nav-title">{{ __('Teacher') }}</li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">
                            <i class="nav-icon icon-notebook"></i> {{ __('Assign activities') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('actividades.plantillas') }}">
                            <i class="nav-icon far fa-copy"></i> {{ __('Activity templates') }}
                        </a>
                    </li>
                @endif
            @endauth
            @auth
                @if(Auth::user()->hasRole('admin'))
                    <li class="nav-title">{{ __('Admin') }}</li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('intellij_projects.copia') }}">
                            <i class="nav-icon far fa-copy"></i> {{ __('Project cloner') }}
                        </a>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="nav-icon icon-book-open"></i> {{ __('Structure') }}
                        </a>
                        <ul class="nav-dropdown-items">
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
                            <i class="nav-icon icon-book-open"></i> {{ __('Resources') }}
                        </a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('youtube_videos.index') }}">
                                    {{ __('YouTube videos') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('intellij_projects.index') }}">
                                    {{ __('IntelliJ projects') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="nav-icon icon-question"></i> Tarjetas de prueba
                        </a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/tarjeta_si_no') }}">
                                    <i class="nav-icon icon-question"></i> Sí/No
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/tarjeta_video') }}">
                                    <i class="nav-icon icon-question"></i> Vídeo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/tarjeta_respuesta_multiple') }}">
                                    <i class="nav-icon icon-question"></i> Test
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/tarjeta_respuesta_corta') }}">
                                    <i class="nav-icon icon-question"></i> Escribir
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/tarjeta_texto_markdown') }}">
                                    <i class="nav-icon icon-question"></i> Texto
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/tarjeta_pdf') }}">
                                    <i class="nav-icon icon-question"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            @endauth
            {{--
                        @auth
                            <li class="nav-title">Profesor</li>
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
                        @endauth
            --}}
            <li class="nav-item mt-auto">
                <a class="nav-link" href="mailto:info@ikasgela.com">
                    <i class="nav-icon icon-envelope"></i> {{ __('Contact') }}</a>
            </li>
            @auth
                {{--
                                <li class="nav-item">
                                    <a class="nav-link nav-link-success" href="{{ url('/documentacion') }}">
                                        <i class="nav-icon icon-docs"></i> {{ __('Help') }}</a>
                                </li>
                --}}
                @if(Auth::user()->hasAnyRole(['admin','profesor']))
                    {{--
                                        <li class="nav-item">
                                            <a class="nav-link nav-link-danger" href="https://coreui.io/pro/">
                                                <i class="nav-icon icon-layers"></i> Premium
                                            </a>
                                        </li>
                    --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="nav-icon icon-settings"></i> {{ __('Settings') }}
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
