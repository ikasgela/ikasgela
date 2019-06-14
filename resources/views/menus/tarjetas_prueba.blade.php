<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
        <i class="nav-icon fas fa-bug"></i> Tarjetas de prueba
    </a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/tarjeta_si_no') }}">
                Sí/No
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/tarjeta_video') }}">
                Vídeo
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/tarjeta_respuesta_multiple') }}">
                Test
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/tarjeta_respuesta_corta') }}">
                Escribir
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/tarjeta_texto_markdown') }}">
                Texto
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/tarjeta_pdf') }}">
                PDF
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/reloj') }}">
                Reloj
            </a>
        </li>
    </ul>
</li>
