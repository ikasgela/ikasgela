<div class="table-responsive">
    <table class="table table-hover">
        <thead class="thead-dark">
        <tr>
            <th>
                <input type="checkbox" id="seleccionar_usuarios">
            </th>
            <th>#</th>
            <th></th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th class="text-center">{{ __('Courses') }}</th>
            <th class="text-center">{{ __('Verified') }}</th>
            <th class="text-center">{{ __('Tutorial') }}</th>
            <th>{{ __('Roles') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>
                    <input form="asignar" type="checkbox"
                           name="usuarios_seleccionados[{{ $user->id }}]" value="{{ $user->id }}">
                </td>
                <td>{{ $user->id }}</td>
                <td>@include('users.partials.avatar', ['user' => $user, 'width' => 35])</td>
                <td>
                    {{ $user->name }} {{ $user->surname }}
                    @include('profesor.partials.status_usuario_filtro')
                </td>
                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                <td class="text-center">{{ $user->cursos()->count() }}</td>
                <td class="text-center">{!! $user->hasVerifiedEmail() ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td class="text-center">{!! $user->tutorial ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                <td>
                    @foreach($user->roles as $rol)
                        {{ !$loop->last ? $rol->name . ', ' : $rol->name }}
                    @endforeach
                </td>
                <td>
                    @include('users.partials.acciones')
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="bg-dark">
            <th colspan="42" class="m-0 py-1"></th>
            @if(Route::currentRouteName() == 'actividades.index')
                <th class="m-0 py-1"></th>
            @endif
        </tr>
        <tr>
            <td colspan="42">
                <div class="form-inline">
                    {!! Form::open(['route' => ['users.matricular'], 'method' => 'POST', 'id' => 'asignar']) !!}
                    <button title="{{ __('Enroll') }}"
                            type="submit"
                            class="btn btn-light btn-sm mr-2"><i class="fas fa-plus"></i>
                    </button>
                    {{ __('on course') }}
                    <select class="form-control ml-2" id="curso_id" name="curso_id">
                        @foreach($cursos as $curso)
                            <option
                                value="{{ $curso->id }}">{{ $curso->full_name }}</option>
                            </option>
                        @endforeach
                    </select>
                    {!! Form::close() !!}
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
