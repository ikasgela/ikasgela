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
                <td>@include('partials.mailto', ['user' => $user])</td>
                <td class="text-center">{{ $user->cursos()->count() }}</td>
                <td class="text-center">@include('partials.check_yes_no', ['checked' => $user->hasVerifiedEmail()])</td>
                <td class="text-center">@include('partials.check_yes_no', ['checked' => $user->tutorial])</td>
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
                    {!! Form::open(['route' => ['users.acciones_grupo'], 'method' => 'POST', 'id' => 'asignar']) !!}
                    <button title="{{ __('Enroll') }}"
                            type="submit"
                            name="action" value="enroll"
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
                    <div class="btn-group mx-4">
                        <button title="{{ __('Manual verification') }}"
                                type="submit"
                                name="action" value="verify"
                                class="btn btn-light btn-sm"><i class="fas fa-user-check"></i>
                        </button>
                        <button title="{{ __('Block') }}"
                                type="submit"
                                name="action" value="block"
                                class="btn btn-light btn-sm"><i class="fas fa-lock"></i>
                        </button>
                        <button title="{{ __('Unblock') }}"
                                type="submit"
                                name="action" value="unblock"
                                class="btn btn-light btn-sm"><i class="fas fa-unlock"></i>
                        </button>
                        <button title="{{ __('Delete') }}"
                                type="submit" onclick="return confirm('{{ __('Are you sure?') }}')"
                                name="action" value="delete"
                                class="btn btn-light btn-sm"><i class="fas fa-trash text-danger"></i>
                        </button>
                    </div>
                    <input type="text" class="form-control mr-2" size="30" name="tags" id="tags"
                           placeholder="{{ __('Tags, separated by commas') }}"/>
                    <button title="{{ __('Add tags') }}"
                            type="submit"
                            name="action" value="tag"
                            class="btn btn-light btn-sm"><i class="fas fa-tag"></i>
                    </button>
                    {!! Form::close() !!}
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
