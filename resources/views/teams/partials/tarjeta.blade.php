<div class="card">
    <div class="card-header"><i class="fas fa-users mr-2"></i>{{ __('Teamwork') }}</div>
    <div class="card-body">
        @forelse($teams as $team)
            <h5 class="card-title">{{ $team->name }}</h5>
            <ul class="list-group"> @foreach($team->users as $user)
                    <li class="list-group-item">
                        @include('users.partials.avatar', ['user' => $user, 'width' => 32])
                        <span class="mx-2">{{ $user->name }} {{ $user->surname }}</span>
                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                    </li>
                @endforeach
            </ul>
        @empty
            <p class="card-text">No hay equipos definidos para esta actividad.</p>
        @endforelse
        <hr>
        <p class="card-text">⚠️ <strong>Atención: ¡tus cambios afectan al resto del equipo!</strong> ⚠️</p>
        <p class="card-text">Es suficiente con que un componente del equipo acepte/clone/envíe la tarea, los cambios de
            estado se
            propagan a todos.️</p>
        <p class="card-text">Recuerda:</p>
        <ul>
            <li>Descargar los últimos cambios usando <em>Update Project</em> antes de hacer nuevas
                modificaciones para tener la versión más actual del repositorio en tu ordenador.
            </li>
            <li>Subir los cambios al servidor mediante <em>Commit and Push</em> para compartirlos con el resto
                del equipo.
            </li>
        </ul>
    </div>
</div>
