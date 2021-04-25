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
        <p class="card-text">⚠️ Atención: ¡tus cambios afectan al resto del equipo! ⚠️</p>
        <ul>
            <li>Es suficiente con que un componente del equipo acepte/clone/envíe la tarea, los cambios de estado se
                propagan a todos.
            </li>
            <li>Recuerda descargar los últimos cambios usando Update Project antes de hacer nuevas modificaciones para
                tener la versión más actual del repositorio en tu ordenador.
            </li>
            <li>Recuerda subir los cambios a Gitea mediante Commit and Push para compartirlos con el resto del equipo.
            </li>
        </ul>
    </div>
</div>
